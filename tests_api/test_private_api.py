"""Pruebas de la API privada: autenticación, postulaciones y prácticas."""
import io

import pytest

from conftest import Api, login

# ------------------------------------------------ Autenticación

class TestAuth:
    def test_login_ok(self):
        r = login("juan@ucsc.cl")
        assert r.status_code == 200
        body = r.json()
        assert body["token_type"] == "Bearer"
        assert len(body["token"]) >= 40
        assert "Estudiante" in body["user"]["roles"]

    def test_login_wrong_password(self):
        r = login("juan@ucsc.cl", "clave-incorrecta")
        assert r.status_code == 401
        assert r.json()["message"] == "Credenciales inválidas."

    def test_login_validation_error(self):
        r = Api().post("/auth/login", json={"email": "no-es-correo"})
        assert r.status_code == 422
        assert {"email", "password"} <= set(r.json()["errors"])

    def test_private_route_without_token_is_401(self, anon):
        assert anon.get("/auth/me").status_code == 401
        assert anon.get("/applications").status_code == 401
        assert anon.get("/practices").status_code == 401

    def test_invalid_token_is_401(self):
        assert Api("token-falso").get("/auth/me").status_code == 401

    def test_me_returns_authenticated_user(self, student):
        r = student.get("/auth/me")
        assert r.status_code == 200
        assert r.json()["data"]["email"] == "juan@ucsc.cl"

    def test_logout_revokes_token(self):
        api = Api(login("maria@ucsc.cl").json()["token"])
        assert api.get("/auth/me").status_code == 200
        assert api.post("/auth/logout").status_code == 200
        assert api.get("/auth/me").status_code == 401


# ------------------------------------------------ API Privada 1: postulaciones

class TestApplications:
    def test_student_lists_only_own_applications(self, student):
        r = student.get("/applications")
        assert r.status_code == 200
        body = r.json()
        assert "meta" in body
        assert all(a["estudiante"]["nombre"].startswith("Juan") for a in body["data"])

    def test_admin_sees_at_least_as_many_as_student(self, student, admin):
        mine = student.get("/applications").json()["meta"]["total"]
        everything = admin.get("/applications").json()["meta"]["total"]
        assert everything >= mine

    def test_coordinator_and_company_can_list(self, coordinator, company):
        assert coordinator.get("/applications").status_code == 200
        assert company.get("/applications").status_code == 200

    def test_filter_by_status(self, admin):
        data = admin.get("/applications", params={"status": "Rechazado"}).json()["data"]
        assert all(a["estado"] == "Rechazado" for a in data)

    def test_show_own_application(self, student):
        items = student.get("/applications").json()["data"]
        if not items:
            pytest.skip("El estudiante de prueba no tiene postulaciones.")
        r = student.get(f"/applications/{items[0]['id']}")
        assert r.status_code == 200
        assert "mensajes" in r.json()["data"]

    def test_unknown_application_is_404(self, student):
        assert student.get("/applications/99999999").status_code == 404

    def test_apply_requires_fields(self, student):
        r = student.post("/applications", data={})
        assert r.status_code == 422
        assert {"offer_id", "carta_motivacion", "cv"} <= set(r.json()["errors"])

    def test_apply_rejects_invalid_file_type(self, student):
        r = student.post("/applications", data={"offer_id": 1, "carta_motivacion": "Hola"},
                         files={"cv": ("virus.exe", io.BytesIO(b"MZ"), "application/octet-stream")})
        assert r.status_code == 422
        assert "cv" in r.json()["errors"]

    def test_only_students_can_apply(self, admin):
        r = admin.post("/applications", data={"offer_id": 1, "carta_motivacion": "Hola"},
                       files={"cv": ("cv.pdf", io.BytesIO(b"%PDF-1.4 test"), "application/pdf")})
        assert r.status_code == 403

    def test_apply_flow_and_withdraw(self, student, anon):
        """Postula a una oferta aprobada; si las reglas de negocio lo impiden, debe responder 422 (nunca 5xx)."""
        offers = anon.get("/public/offers", params={"per_page": 50}).json()["data"]
        if not offers:
            pytest.skip("No hay ofertas aprobadas.")

        created = None
        for offer in offers:
            r = student.post("/applications", data={"offer_id": offer["id"], "carta_motivacion": "Prueba automática de la API."},
                             files={"cv": ("cv.pdf", io.BytesIO(b"%PDF-1.4 test"), "application/pdf")})
            assert r.status_code in (201, 422), r.text
            if r.status_code == 201:
                created = r.json()["data"]
                break

        if created is None:
            pytest.skip("Ninguna oferta permite postular con el estudiante de prueba (reglas de negocio).")

        assert created["estado"] == "Postulado"
        assert student.get(f"/applications/{created['id']}").status_code == 200
        # no se puede postular dos veces a la misma oferta
        again = student.post("/applications", data={"offer_id": created["oferta"]["id"], "carta_motivacion": "Duplicada"},
                             files={"cv": ("cv.pdf", io.BytesIO(b"%PDF-1.4 test"), "application/pdf")})
        assert again.status_code == 422
        # retirar la postulación
        assert student.delete(f"/applications/{created['id']}").status_code == 200
        assert student.get(f"/applications/{created['id']}").status_code == 404

    def test_non_student_cannot_withdraw(self, admin):
        assert admin.delete("/applications/1").status_code == 403


# ------------------------------------------------ API Privada 2: prácticas y bitácoras

class TestPractices:
    def test_lists_by_role(self, student, admin, coordinator, company):
        for api in (student, admin, coordinator, company):
            r = api.get("/practices")
            assert r.status_code == 200
            assert "meta" in r.json()

    def test_filter_by_estado(self, admin):
        data = admin.get("/practices", params={"estado": "En Curso"}).json()["data"]
        assert all(p["estado"] == "En Curso" for p in data)

    def test_practice_detail_has_evaluations_and_hours(self, admin):
        items = admin.get("/practices").json()["data"]
        if not items:
            pytest.skip("No hay prácticas en la base de datos.")
        r = admin.get(f"/practices/{items[0]['id']}")
        assert r.status_code == 200
        data = r.json()["data"]
        assert {"horas_registradas", "evaluaciones", "nota_final_calculada", "resena_empresa"} <= set(data)

    def test_logs_list(self, admin):
        items = admin.get("/practices").json()["data"]
        if not items:
            pytest.skip("No hay prácticas en la base de datos.")
        r = admin.get(f"/practices/{items[0]['id']}/logs")
        assert r.status_code == 200
        assert "horas_totales" in r.json()["meta"]

    def test_unknown_practice_is_404(self, admin):
        assert admin.get("/practices/99999999").status_code == 404
        assert admin.get("/practices/99999999/logs").status_code == 404

    def test_admin_cannot_write_students_log(self, admin):
        items = admin.get("/practices").json()["data"]
        if not items:
            pytest.skip("No hay prácticas en la base de datos.")
        r = admin.post(f"/practices/{items[0]['id']}/logs", json={})
        assert r.status_code == 403

    def test_student_log_validation_and_creation(self, student):
        practices = student.get("/practices").json()["data"]
        if not practices:
            pytest.skip("El estudiante de prueba no tiene prácticas.")
        practice = practices[0]

        if practice["estado"] not in ("En Curso", "Informe Entregado"):
            r = student.post(f"/practices/{practice['id']}/logs", json={})
            assert r.status_code == 409
            return

        # validación
        bad = student.post(f"/practices/{practice['id']}/logs", json={"hours": 500})
        assert bad.status_code == 422
        assert {"period_start", "period_end", "hours", "activities"} <= set(bad.json()["errors"])

        # creación correcta
        before = student.get(f"/practices/{practice['id']}/logs").json()["meta"]["total"]
        ok = student.post(f"/practices/{practice['id']}/logs", json={
            "period_start": "2026-11-02", "period_end": "2026-11-13", "hours": 80,
            "activities": "Prueba automática: desarrollo de pruebas de integración de la API.",
        })
        assert ok.status_code == 201, ok.text
        assert ok.json()["data"]["horas"] == 80
        after = student.get(f"/practices/{practice['id']}/logs").json()["meta"]["total"]
        assert after == before + 1

    def test_period_longer_than_fortnight_is_rejected(self, student):
        practices = [p for p in student.get("/practices").json()["data"] if p["estado"] in ("En Curso", "Informe Entregado")]
        if not practices:
            pytest.skip("No hay una práctica en curso del estudiante de prueba.")
        r = student.post(f"/practices/{practices[0]['id']}/logs", json={
            "period_start": "2026-09-01", "period_end": "2026-10-30", "hours": 80,
            "activities": "Período demasiado largo para una sola bitácora quincenal.",
        })
        assert r.status_code == 422
        assert "period_end" in r.json()["errors"]
