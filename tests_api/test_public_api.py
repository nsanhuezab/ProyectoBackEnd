"""Pruebas de las 2 APIs públicas (sin token)."""
import pytest

# ------------------------------------------------ API Pública 1: ofertas

class TestPublicOffers:
    def test_list_returns_data_and_meta(self, anon):
        r = anon.get("/public/offers")
        assert r.status_code == 200
        body = r.json()
        assert isinstance(body["data"], list)
        assert {"total", "page", "per_page", "last_page"} <= set(body["meta"])

    def test_list_only_approved_offers_have_expected_fields(self, anon):
        items = anon.get("/public/offers").json()["data"]
        for offer in items:
            assert {"id", "titulo", "empresa", "modalidad", "vacantes_disponibles", "carreras"} <= set(offer)
            assert offer["vacantes_disponibles"] >= 0

    def test_per_page_limits_results(self, anon):
        body = anon.get("/public/offers", params={"per_page": 2}).json()
        assert len(body["data"]) <= 2
        assert body["meta"]["per_page"] == 2

    def test_per_page_above_max_is_rejected(self, anon):
        assert anon.get("/public/offers", params={"per_page": 500}).status_code == 422

    def test_filter_by_modalidad(self, anon):
        items = anon.get("/public/offers", params={"modalidad": "Remota"}).json()["data"]
        assert all(o["modalidad"] == "Remota" for o in items)

    def test_search_filters_results(self, anon):
        r = anon.get("/public/offers", params={"search": "zzzz-no-existe-zzzz"})
        assert r.status_code == 200
        assert r.json()["data"] == []

    def test_show_detail(self, anon):
        items = anon.get("/public/offers").json()["data"]
        if not items:
            pytest.skip("No hay ofertas aprobadas en la base de datos.")
        r = anon.get(f"/public/offers/{items[0]['id']}")
        assert r.status_code == 200
        data = r.json()["data"]
        assert data["id"] == items[0]["id"]
        assert {"perfil", "habilidades", "horario"} <= set(data)

    def test_show_unknown_offer_is_404(self, anon):
        r = anon.get("/public/offers/99999999")
        assert r.status_code == 404
        assert "message" in r.json()

    def test_non_numeric_id_is_404(self, anon):
        assert anon.get("/public/offers/abc").status_code == 404


# ------------------------------------------------ API Pública 2: carreras y empresas

class TestPublicDirectory:
    def test_careers_list(self, anon):
        r = anon.get("/public/careers")
        assert r.status_code == 200
        data = r.json()["data"]
        assert isinstance(data, list)
        for career in data:
            assert {"id", "codigo", "nombre"} <= set(career)

    def test_companies_list_hides_private_data(self, anon):
        r = anon.get("/public/companies")
        assert r.status_code == 200
        for company in r.json()["data"]:
            assert {"id", "razon_social", "rating_promedio", "ofertas_aprobadas"} <= set(company)
            assert "rut" not in company and "contacto_email" not in company and "telefono" not in company

    def test_companies_search_without_results(self, anon):
        body = anon.get("/public/companies", params={"search": "zzzz-no-existe-zzzz"}).json()
        assert body["data"] == []
        assert body["meta"]["total"] == 0

    def test_company_detail_includes_offers_and_reviews(self, anon):
        items = anon.get("/public/companies").json()["data"]
        if not items:
            pytest.skip("No hay empresas en la base de datos.")
        r = anon.get(f"/public/companies/{items[0]['id']}")
        assert r.status_code == 200
        data = r.json()["data"]
        assert isinstance(data["ofertas"], list) and isinstance(data["resenas"], list)

    def test_unknown_company_is_404(self, anon):
        assert anon.get("/public/companies/99999999").status_code == 404

    def test_unknown_route_returns_json_404(self, anon):
        r = anon.get("/public/no-existe")
        assert r.status_code == 404
        assert r.headers["Content-Type"].startswith("application/json")
