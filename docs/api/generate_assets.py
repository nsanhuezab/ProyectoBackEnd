#!/usr/bin/env python3
"""
Genera, desde una única especificación, los 3 artefactos de documentación de la API:

  docs/api/endpoints.json                          -> lo lee la vista /docs/api
  docs/postman/ProyectoBackEnd.postman_collection.json
  docs/postman/ProyectoBackEnd.postman_environment.json

Uso (desde la raíz del proyecto):   python docs/api/generate_assets.py
"""
import json
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
BASE_DEFAULT = "http://127.0.0.1:8000/api/v1"


def P(name, typ, desc, example=None, required=False, where="query"):
    return {"name": name, "in": where, "type": typ, "required": required, "description": desc, "example": example}


PAGINATION = [
    P("per_page", "integer", "Resultados por página (1 a 50, por defecto 10).", 10),
    P("page", "integer", "Número de página.", 1),
]

OFFER_LIST_ITEM = {
    "id": 3, "titulo": "Practicante de Desarrollo Web", "empresa": "TechSur SpA", "area": "Tecnología",
    "modalidad": "Híbrida", "ubicacion": "Concepción", "duracion_semanas": 16, "vacantes": 2,
    "vacantes_disponibles": 2, "carreras": [{"id": 1, "nombre": "Ingeniería Civil Informática"}],
}
META = {"total": 12, "page": 1, "per_page": 10, "last_page": 2}
APPLICATION_ITEM = {
    "id": 7, "estado": "Postulado", "afinidad": 75.0, "tiene_cv": True,
    "fecha_postulacion": "2026-11-10T14:32:00-03:00", "fecha_decision": None,
    "estudiante": {"id": 1, "nombre": "Juan Pérez", "carrera": "Ingeniería Civil Informática"},
    "oferta": {"id": 3, "titulo": "Practicante de Desarrollo Web", "empresa": "TechSur SpA"},
}
PRACTICE_ITEM = {
    "id": 2, "estado": "En Curso", "fecha_inicio": "2026-11-01", "fecha_termino": "2027-02-20",
    "nota_final": None, "contratado": False,
    "estudiante": {"nombre": "Juan Pérez", "carrera": "Ingeniería Civil Informática"},
    "oferta": {"id": 3, "titulo": "Practicante de Desarrollo Web", "empresa": "TechSur SpA"},
}
LOG_ITEM = {
    "id": 5, "desde": "2026-11-02", "hasta": "2026-11-13", "horas": 80,
    "actividades": "Desarrollo de vistas del módulo de reportes y pruebas de integración.",
    "aprendizajes": "Uso de Git flow.", "dificultades": None,
}
E401 = {"status": 401, "description": "Token ausente, inválido o expirado.", "example": {"message": "Token inválido o expirado."}}
E403 = {"status": 403, "description": "El rol del usuario no tiene permiso.", "example": {"message": "Tu rol no tiene acceso a las postulaciones."}}
E404 = lambda what: {"status": 404, "description": f"{what} no existe o no es visible para el usuario.", "example": {"message": f"{what} no encontrada."}}
E422 = {"status": 422, "description": "Error de validación.", "example": {"message": "The hours field is required.", "errors": {"hours": ["The hours field is required."]}}}


def ep(eid, method, path, summary, *, auth, params=None, body=None, body_type=None, roles=None,
       ok=200, ok_example=None, errors=None, idvar=None, expect_js=None, note=None):
    responses = [{"status": ok, "description": "Respuesta exitosa.", "example": ok_example}]
    responses += errors or []
    return {
        "id": eid, "method": method, "path": path, "summary": summary, "auth": auth, "roles": roles or [],
        "params": params or [], "body": body or [], "body_type": body_type, "responses": responses,
        "idvar": idvar, "expect_status": ok, "expect_js": expect_js or [], "note": note,
    }


GROUPS = [
    {
        "id": "public-offers", "name": "API Pública 1 · Ofertas de práctica", "visibility": "public",
        "description": "Consulta abierta (sin token) de las ofertas de práctica que ya fueron aprobadas por coordinación. Límite: 60 solicitudes por minuto por IP.",
        "endpoints": [
            ep("offers-list", "GET", "/public/offers", "Listar ofertas aprobadas", auth=False,
               params=[P("search", "string", "Texto a buscar en título, empresa o área.", "desarrollo"),
                       P("career_id", "integer", "Solo ofertas dirigidas a esta carrera.", 1),
                       P("modalidad", "string", "Presencial, Remota o Híbrida.", "Híbrida"),
                       P("area", "string", "Área de la oferta (búsqueda parcial).", "Tecnología"),
                       P("ubicacion", "string", "Ciudad o región (búsqueda parcial).", "Concepción")] + PAGINATION,
               ok_example={"data": [OFFER_LIST_ITEM], "meta": META},
               errors=[E422],
               expect_js=["const j = pm.response.json();", "pm.test('incluye data y meta', () => { pm.expect(j).to.have.property('data'); pm.expect(j).to.have.property('meta'); });",
                          "if (j.data.length) { pm.collectionVariables.set('offer_id', j.data[0].id); }"]),
            ep("offers-show", "GET", "/public/offers/{id}", "Detalle de una oferta aprobada", auth=False, idvar="offer_id",
               params=[P("id", "integer", "ID de la oferta.", 3, True, "path")],
               ok_example={"data": {**OFFER_LIST_ITEM, "perfil": "Estudiante de 4.º año con conocimientos de Laravel.", "nivel_requerido": "Avanzado",
                                    "promedio_minimo": 5.0, "horario": "Lunes a viernes 9:00–14:00", "remuneracion": 250000.0,
                                    "habilidades": ["PHP", "Laravel", "MySQL"], "rubro_empresa": "Software"}},
               errors=[E404("Oferta")],
               expect_js=["pm.test('tiene id y título', () => { const d = pm.response.json().data; pm.expect(d).to.have.property('id'); pm.expect(d).to.have.property('titulo'); });"]),
        ],
    },
    {
        "id": "public-directory", "name": "API Pública 2 · Carreras y empresas", "visibility": "public",
        "description": "Directorio abierto de carreras activas y empresas colaboradoras con su valoración promedio. No expone RUT ni datos de contacto. Límite: 60 solicitudes por minuto por IP.",
        "endpoints": [
            ep("careers-list", "GET", "/public/careers", "Listar carreras activas", auth=False,
               ok_example={"data": [{"id": 1, "codigo": "ICI", "nombre": "Ingeniería Civil Informática"}]},
               expect_js=["pm.test('data es un arreglo', () => pm.expect(pm.response.json().data).to.be.an('array'));"]),
            ep("companies-list", "GET", "/public/companies", "Listar empresas", auth=False,
               params=[P("search", "string", "Texto a buscar en razón social o rubro.", "tech"),
                       P("rubro", "string", "Rubro (búsqueda parcial).", "Software")] + PAGINATION,
               ok_example={"data": [{"id": 1, "razon_social": "TechSur SpA", "rubro": "Software", "sitio_web": "https://techsur.cl",
                                     "direccion": "Barros Arana 100, Concepción", "rating_promedio": 4.5, "total_resenas": 4, "ofertas_aprobadas": 2}], "meta": META},
               expect_js=["const j = pm.response.json();", "pm.expect(j).to.have.property('meta');", "if (j.data.length) { pm.collectionVariables.set('company_id', j.data[0].id); }"]),
            ep("companies-show", "GET", "/public/companies/{id}", "Detalle de una empresa", auth=False, idvar="company_id",
               params=[P("id", "integer", "ID de la empresa.", 1, True, "path")],
               ok_example={"data": {"id": 1, "razon_social": "TechSur SpA", "rubro": "Software", "sitio_web": "https://techsur.cl", "direccion": "Barros Arana 100, Concepción",
                                    "rating_promedio": 4.5, "total_resenas": 4, "ofertas_aprobadas": 2,
                                    "ofertas": [{"id": 3, "titulo": "Practicante de Desarrollo Web", "modalidad": "Híbrida", "ubicacion": "Concepción"}],
                                    "resenas": [{"valoracion": 5, "comentario": "Excelente equipo.", "fecha": "2026-11-09"}]}},
               errors=[E404("Empresa")],
               expect_js=["pm.test('incluye ofertas y reseñas', () => { const d = pm.response.json().data; pm.expect(d).to.have.property('ofertas'); pm.expect(d).to.have.property('resenas'); });"]),
        ],
    },
    {
        "id": "auth", "name": "Autenticación (API privada)", "visibility": "private",
        "description": "La API privada usa tokens Bearer. Se obtiene un token con /auth/login y se envía en cada solicitud como  Authorization: Bearer <token>. El token dura 7 días y se puede revocar con /auth/logout. Login: máximo 10 intentos por minuto.",
        "endpoints": [
            ep("auth-login", "POST", "/auth/login", "Iniciar sesión y obtener token", auth=False, body_type="json",
               body=[P("email", "string", "Correo del usuario.", "juan@ucsc.cl", True, "body"),
                     P("password", "string", "Contraseña.", "12345678", True, "body"),
                     P("device_name", "string", "Nombre del dispositivo (opcional).", "postman", False, "body")],
               ok_example={"token": "q8Wk...64 caracteres...Zr", "token_type": "Bearer", "expires_at": "2026-11-20T10:00:00-03:00",
                           "user": {"id": 2, "name": "Juan", "last_name": "Pérez", "email": "juan@ucsc.cl", "career_id": 1, "roles": ["Estudiante"]}},
               errors=[{"status": 401, "description": "Credenciales incorrectas.", "example": {"message": "Credenciales inválidas."}}, E422],
               expect_js=["const j = pm.response.json();", "pm.test('entrega token Bearer', () => { pm.expect(j.token).to.be.a('string'); pm.expect(j.token_type).to.eql('Bearer'); });",
                          "pm.collectionVariables.set('token', j.token);"],
               note="Usuarios de prueba (seeders), contraseña 12345678: admin@ucsc.cl (Administrador), juan@ucsc.cl y maria@ucsc.cl (Estudiante), coordinador@ucsc.cl (Coordinador), empresa@gmail.com (Empresa)."),
            ep("auth-me", "GET", "/auth/me", "Datos del usuario autenticado", auth=True,
               ok_example={"data": {"id": 2, "name": "Juan", "last_name": "Pérez", "email": "juan@ucsc.cl", "career_id": 1, "roles": ["Estudiante"]}},
               errors=[E401], expect_js=["pm.test('devuelve roles', () => pm.expect(pm.response.json().data.roles).to.be.an('array'));"]),
            ep("auth-logout", "POST", "/auth/logout", "Cerrar sesión (revoca el token actual)", auth=True,
               ok_example={"message": "Sesión cerrada. El token fue revocado."}, errors=[E401],
               note="En Postman, después de ejecutar este request vuelve a ejecutar el login para obtener un token nuevo."),
        ],
    },
    {
        "id": "private-applications", "name": "API Privada 1 · Postulaciones", "visibility": "private",
        "description": "Gestión de postulaciones a ofertas. Cada rol solo ve lo suyo: el estudiante sus postulaciones, el coordinador las de su carrera, la empresa las de sus ofertas y el administrador todas.",
        "endpoints": [
            ep("applications-list", "GET", "/applications", "Listar postulaciones visibles", auth=True,
               roles=["Estudiante", "Coordinador", "Empresa", "Administrador"],
               params=[P("status", "string", "Filtra por estado: Postulado, Asignado o Rechazado.", "Postulado")] + PAGINATION,
               ok_example={"data": [APPLICATION_ITEM], "meta": META}, errors=[E401, E403],
               expect_js=["const j = pm.response.json();", "pm.expect(j).to.have.property('meta');", "if (j.data.length) { pm.collectionVariables.set('application_id', j.data[0].id); }"]),
            ep("applications-show", "GET", "/applications/{id}", "Detalle de una postulación (con mensajes)", auth=True, idvar="application_id",
               roles=["Estudiante", "Coordinador", "Empresa", "Administrador"],
               params=[P("id", "integer", "ID de la postulación.", 7, True, "path")],
               ok_example={"data": {**APPLICATION_ITEM, "motivacion": "Me interesa aplicar lo aprendido en proyectos reales.",
                                    "mensajes": [{"id": 1, "user_id": 3, "estado_postulacion": "Postulado", "mensaje": "Hola, revisamos tu CV.", "fecha": "2026-11-11T09:00:00-03:00"}]}},
               errors=[E401, E404("Postulación")],
               expect_js=["pm.test('incluye mensajes', () => pm.expect(pm.response.json().data).to.have.property('mensajes'));"]),
            ep("applications-store", "POST", "/applications", "Postular a una oferta (funcionalidad crítica)", auth=True, body_type="multipart",
               roles=["Estudiante"],
               body=[P("offer_id", "integer", "ID de una oferta aprobada.", 3, True, "body"),
                     P("carta_motivacion", "string", "Carta de motivación (máx. 10000 caracteres).", "Me interesa esta práctica porque...", True, "body"),
                     P("cv", "file", "Currículum en PDF, DOC o DOCX (máx. 5 MB).", "cv.pdf", True, "body")],
               ok=201, ok_example={"data": APPLICATION_ITEM},
               errors=[E401, {"status": 403, "description": "El usuario no es estudiante.", "example": {"message": "Solo los estudiantes pueden postular."}},
                       {"status": 422, "description": "Reglas de negocio o validación: oferta no aprobada, sin cupos, carrera distinta, promedio insuficiente o postulación duplicada.",
                        "example": {"message": "La oferta ya no tiene cupos disponibles.", "errors": {"offer_id": ["La oferta ya no tiene cupos disponibles."]}}}],
               expect_js=["pm.test('postulación creada en estado Postulado', () => pm.expect(pm.response.json().data.estado).to.eql('Postulado'));",
                          "pm.collectionVariables.set('new_application_id', pm.response.json().data.id);"],
               note="Se envía como multipart/form-data. En Postman, selecciona un archivo en el campo «cv» antes de enviar."),
            ep("applications-destroy", "DELETE", "/applications/{id}", "Retirar una postulación propia", auth=True, idvar="new_application_id",
               roles=["Estudiante"], params=[P("id", "integer", "ID de la postulación (debe estar en estado Postulado).", 7, True, "path")],
               ok_example={"message": "Postulación retirada."},
               errors=[E401, E403, E404("Postulación"), {"status": 409, "description": "La postulación ya fue resuelta.", "example": {"message": "No puedes retirar una postulación que ya fue resuelta."}}]),
        ],
    },
    {
        "id": "private-practices", "name": "API Privada 2 · Prácticas y bitácoras", "visibility": "private",
        "description": "Consulta de prácticas con sus evaluaciones y registro de la bitácora quincenal. Mismo alcance por rol que las postulaciones.",
        "endpoints": [
            ep("practices-list", "GET", "/practices", "Listar prácticas visibles", auth=True, roles=["Estudiante", "Coordinador", "Empresa", "Administrador"],
               params=[P("estado", "string", "Asignado, En Curso, Informe Entregado, Evaluado o Finalizada.", "En Curso")] + PAGINATION,
               ok_example={"data": [PRACTICE_ITEM], "meta": META}, errors=[E401, E403],
               expect_js=["const j = pm.response.json();", "pm.expect(j).to.have.property('meta');", "if (j.data.length) { pm.collectionVariables.set('practice_id', j.data[0].id); }"]),
            ep("practices-show", "GET", "/practices/{id}", "Detalle de práctica (horas, evaluaciones, nota final, reseña)", auth=True, idvar="practice_id",
               roles=["Estudiante", "Coordinador", "Empresa", "Administrador"], params=[P("id", "integer", "ID de la práctica.", 2, True, "path")],
               ok_example={"data": {**PRACTICE_ITEM, "horas_registradas": 160, "nota_final_calculada": 6.3,
                                    "evaluaciones": [{"rol": "supervisor", "nota": 6.5, "puntajes": {"responsabilidad": 6.5, "calidad": 6.5}, "comentarios": "Muy buen desempeño."}],
                                    "resena_empresa": {"valoracion": 5, "comentario": "Excelente experiencia."}}},
               errors=[E401, E404("Práctica")],
               expect_js=["pm.test('incluye evaluaciones', () => pm.expect(pm.response.json().data).to.have.property('evaluaciones'));"]),
            ep("practices-logs", "GET", "/practices/{id}/logs", "Listar bitácoras de una práctica", auth=True, idvar="practice_id",
               roles=["Estudiante", "Coordinador", "Empresa", "Administrador"], params=[P("id", "integer", "ID de la práctica.", 2, True, "path")],
               ok_example={"data": [LOG_ITEM], "meta": {"total": 1, "horas_totales": 80}}, errors=[E401, E404("Práctica")],
               expect_js=["pm.expect(pm.response.json().meta).to.have.property('horas_totales');"]),
            ep("practices-logs-store", "POST", "/practices/{id}/logs", "Registrar bitácora quincenal (funcionalidad crítica)", auth=True, idvar="practice_id",
               body_type="json", roles=["Estudiante"], params=[P("id", "integer", "ID de la práctica propia.", 2, True, "path")],
               body=[P("period_start", "date", "Inicio del período (AAAA-MM-DD, no futuro).", "2026-11-02", True, "body"),
                     P("period_end", "date", "Fin del período (máximo una quincena).", "2026-11-13", True, "body"),
                     P("hours", "integer", "Horas trabajadas (1 a 120).", 80, True, "body"),
                     P("activities", "string", "Actividades realizadas (mínimo 20 caracteres).", "Desarrollo de vistas del módulo de reportes y pruebas de integración.", True, "body"),
                     P("learnings", "string", "Aprendizajes.", "Uso de Git flow.", False, "body"),
                     P("difficulties", "string", "Dificultades.", "", False, "body")],
               ok=201, ok_example={"data": LOG_ITEM},
               errors=[E401, {"status": 403, "description": "El usuario no es el estudiante de la práctica.", "example": {"message": "Solo el estudiante de la práctica puede registrar su bitácora."}},
                       E404("Práctica"),
                       {"status": 409, "description": "La práctica no está «En Curso» ni con informe entregado.", "example": {"message": "La bitácora se registra mientras la práctica está «En Curso» o con el informe entregado."}}, E422],
               expect_js=["pm.test('bitácora creada', () => pm.expect(pm.response.json().data).to.have.property('id'));"]),
        ],
    },
]


def curl_for(e):
    parts = [f'curl -X {e["method"]} "__BASE__{e["path"]}"']
    if e["auth"]:
        parts.append('-H "Authorization: Bearer <TOKEN>"')
    parts.append('-H "Accept: application/json"')
    if e["body_type"] == "json":
        sample = {b["name"]: b["example"] for b in e["body"] if b["required"] or b["example"]}
        parts.append('-H "Content-Type: application/json"')
        parts.append("-d '" + json.dumps(sample, ensure_ascii=False) + "'")
    elif e["body_type"] == "multipart":
        for b in e["body"]:
            parts.append(f'-F "{b["name"]}=@cv.pdf"' if b["type"] == "file" else f'-F "{b["name"]}={b["example"]}"')
    elif e["params"]:
        q = "&".join(f'{p["name"]}={p["example"]}' for p in e["params"][:2] if p["in"] == "query")
        if q:
            parts[0] = parts[0].replace(f'{e["path"]}"', f'{e["path"]}?{q}"')
    return " \\\n  ".join(parts)


for g in GROUPS:
    for e in g["endpoints"]:
        e["curl"] = curl_for(e)

INFO = {
    "title": "API REST · Plataforma de Prácticas Profesionales",
    "version": "v1",
    "base_url": BASE_DEFAULT,
    "description": "API REST en JSON del sistema de gestión de prácticas (Laravel). Incluye 2 APIs públicas y 2 privadas. "
                   "Todas las respuestas son JSON; los errores de validación devuelven 422 con el detalle por campo.",
}

(ROOT / "docs/api/endpoints.json").write_text(json.dumps({"info": INFO, "groups": GROUPS}, ensure_ascii=False, indent=2), encoding="utf-8")


# ----------------------------------------------------------------- Postman v2.1
def pm_item(e):
    path = e["path"].lstrip("/")
    if e["idvar"]:
        path = path.replace("{id}", "{{" + e["idvar"] + "}}")
    segments = path.split("/")
    query = [{"key": p["name"], "value": str(p["example"]), "disabled": True} for p in e["params"] if p["in"] == "query"]
    url = {"raw": "{{base_url}}/" + path, "host": ["{{base_url}}"], "path": segments}
    if query:
        url["query"] = query
    req = {"method": e["method"], "header": [{"key": "Accept", "value": "application/json"}], "url": url, "description": e["summary"] + (("\n\n" + e["note"]) if e["note"] else "")}
    if not e["auth"]:
        req["auth"] = {"type": "noauth"}
    if e["body_type"] == "json":
        sample = {}
        for b in e["body"]:
            v = b["example"]
            if e["id"] == "auth-login":
                v = {"email": "{{email}}", "password": "{{password}}"}.get(b["name"], v)
            if b["required"] or v:
                sample[b["name"]] = v
        req["header"].append({"key": "Content-Type", "value": "application/json"})
        req["body"] = {"mode": "raw", "raw": json.dumps(sample, ensure_ascii=False, indent=2), "options": {"raw": {"language": "json"}}}
    elif e["body_type"] == "multipart":
        form = []
        for b in e["body"]:
            if b["type"] == "file":
                form.append({"key": b["name"], "type": "file", "src": []})
            else:
                value = "{{offer_id}}" if b["name"] == "offer_id" else str(b["example"])
                form.append({"key": b["name"], "value": value, "type": "text"})
        req["body"] = {"mode": "formdata", "formdata": form}
    script = [f"pm.test('Status {e['expect_status']}', () => pm.response.to.have.status({e['expect_status']}));",
              "pm.test('Responde en JSON', () => pm.response.to.be.json);"] + e["expect_js"]
    return {"name": f'{e["method"]} {e["path"]} — {e["summary"]}', "request": req,
            "event": [{"listen": "test", "script": {"type": "text/javascript", "exec": script}}]}


collection = {
    "info": {"name": "ProyectoBackEnd · API REST v1", "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json",
             "description": "Colección de las 2 APIs públicas y 2 privadas. Orden sugerido: carpeta Autenticación → login, luego el resto."},
    "auth": {"type": "bearer", "bearer": [{"key": "token", "value": "{{token}}", "type": "string"}]},
    "item": [{"name": g["name"], "description": g["description"],
              "item": [pm_item(e) for e in g["endpoints"] if e["id"] != "auth-logout"]} for g in GROUPS],
    "variable": [{"key": k, "value": ""} for k in ["token", "offer_id", "company_id", "application_id", "new_application_id", "practice_id"]],
}
# El logout revoca el token: va en una carpeta final para no romper el Runner.
logout = next(e for g in GROUPS for e in g["endpoints"] if e["id"] == "auth-logout")
collection["item"].append({"name": "Cerrar sesión (ejecutar al final)", "item": [pm_item(logout)]})
environment = {
    "name": "ProyectoBackEnd · Local", "_postman_variable_scope": "environment",
    "values": [{"key": "base_url", "value": BASE_DEFAULT, "enabled": True},
               {"key": "email", "value": "juan@ucsc.cl", "enabled": True},
               {"key": "password", "value": "12345678", "enabled": True}],
}
(ROOT / "docs/postman/ProyectoBackEnd.postman_collection.json").write_text(json.dumps(collection, ensure_ascii=False, indent=2), encoding="utf-8")
(ROOT / "docs/postman/ProyectoBackEnd.postman_environment.json").write_text(json.dumps(environment, ensure_ascii=False, indent=2), encoding="utf-8")
print("OK:", sum(len(g["endpoints"]) for g in GROUPS), "endpoints")
