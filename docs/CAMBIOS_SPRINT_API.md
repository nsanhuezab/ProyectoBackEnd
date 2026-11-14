# Cambios: mantenedores 8-12, API REST, documentación, Postman y tests

## Puesta en marcha
```bash
php artisan migrate            # crea la tabla api_tokens
php artisan route:list --path=api   # debe listar 11 rutas bajo /api/v1
php artisan serve
```
- Documentación en vista: http://127.0.0.1:8000/docs/api
- Mantenedores nuevos (rol Administrador): menú lateral → Bitácoras, Evaluaciones, Reseñas de empresa, Mensajes, Roles
- Postman: importar `docs/postman/*.json` (también descargables desde /docs/api)
- Tests Python: ver `tests_api/README.md`

## Mantenedores (12 en total)
Existentes (1-7): Usuarios, Carreras, Coordinadores, Estudiantes, Empresas, Ofertas, Prácticas.
Nuevos (8-12), definidos en `config/catalogs.php` y servidos por `CatalogController` + `resources/views/catalogs/index.blade.php`:
8. Bitácoras de práctica · 9. Evaluaciones de práctica · 10. Reseñas de empresa · 11. Mensajes de postulación · 12. Roles
(Postulaciones se mantiene como módulo de gestión: asignar, rechazar, eliminar.)

## API REST v1  (`routes/api.php`)
| Tipo | API | Endpoints |
|---|---|---|
| Pública 1 | Ofertas | GET /public/offers, GET /public/offers/{id} |
| Pública 2 | Carreras y empresas | GET /public/careers, /public/companies, /public/companies/{id} |
| Privada | Autenticación | POST /auth/login, GET /auth/me, POST /auth/logout |
| Privada 1 | Postulaciones | GET/POST /applications, GET/DELETE /applications/{id} |
| Privada 2 | Prácticas y bitácoras | GET /practices, GET /practices/{id}, GET/POST /practices/{id}/logs |

Autenticación: token Bearer propio (`api_tokens`, guarda solo el hash SHA-256, vence a los 7 días).

## Si cambias la API
Edita la especificación en `docs/api/generate_assets.py` y ejecuta `python docs/api/generate_assets.py`:
regenera `endpoints.json` (vista /docs/api) y la colección/environment de Postman.

## Archivos nuevos
app/Http/Controllers/Admin/CatalogController.php · app/Http/Controllers/Api/V1/* (6) · app/Http/Controllers/ApiDocsController.php ·
app/Http/Middleware/AuthenticateApiToken.php · app/Models/ApiToken.php · config/catalogs.php · routes/api.php ·
database/migrations/2026_11_13_000001_create_api_tokens_table.php · resources/views/catalogs/index.blade.php ·
resources/views/docs/api.blade.php · docs/ · tests_api/
## Archivos modificados
bootstrap/app.php (registra api y alias api.token) · routes/web.php (rutas de mantenedores y /docs/api) · resources/views/layouts/app.blade.php (menú)
