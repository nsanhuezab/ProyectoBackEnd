# Tests de la API REST (Python)

Prueban las 2 APIs públicas y las 2 privadas contra el servidor Laravel en ejecución.

## Preparación
```bash
# 1) En la raíz del proyecto Laravel
php artisan migrate --seed
php artisan serve            # http://127.0.0.1:8000

# 2) En otra terminal
cd tests_api
python -m venv .venv && .venv\Scripts\activate      # Windows  (Linux/Mac: source .venv/bin/activate)
pip install -r requirements.txt
```

## Ejecutar
```bash
pytest -v                    # todo
pytest -v test_public_api.py # solo APIs públicas
pytest -v -k apply           # solo el flujo de postulación
```

Variables opcionales: `API_BASE_URL` (por defecto `http://127.0.0.1:8000/api/v1`) y `API_PASSWORD` (por defecto `12345678`).

Los usuarios de prueba vienen de los seeders: `admin@ucsc.cl`, `juan@ucsc.cl`, `coordinador@ucsc.cl`, `empresa@gmail.com`.
Los tests que necesitan datos concretos (ofertas aprobadas, prácticas en curso) hacen `skip` si la base no los tiene.
