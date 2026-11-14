"""
Configuración compartida de los tests de la API REST.

Variables de entorno (opcionales):
  API_BASE_URL   por defecto http://127.0.0.1:8000/api/v1
  API_PASSWORD   por defecto 12345678 (contraseña de los usuarios de los seeders)
"""
import os

import pytest
import requests

BASE_URL = os.getenv("API_BASE_URL", "http://127.0.0.1:8000/api/v1").rstrip("/")
PASSWORD = os.getenv("API_PASSWORD", "12345678")
TIMEOUT = 15

USERS = {
    "admin": "admin@ucsc.cl",
    "student": "juan@ucsc.cl",
    "coordinator": "coordinador@ucsc.cl",
    "company": "empresa@gmail.com",
}


class Api:
    """Cliente mínimo sobre requests con URL base y token opcional."""

    def __init__(self, token: str | None = None):
        self.session = requests.Session()
        self.session.headers["Accept"] = "application/json"
        if token:
            self.session.headers["Authorization"] = f"Bearer {token}"

    def request(self, method: str, path: str, **kwargs) -> requests.Response:
        return self.session.request(method, f"{BASE_URL}{path}", timeout=TIMEOUT, **kwargs)

    def get(self, path, **kw):
        return self.request("GET", path, **kw)

    def post(self, path, **kw):
        return self.request("POST", path, **kw)

    def delete(self, path, **kw):
        return self.request("DELETE", path, **kw)


def login(email: str, password: str = PASSWORD) -> requests.Response:
    return requests.post(f"{BASE_URL}/auth/login", json={"email": email, "password": password},
                         headers={"Accept": "application/json"}, timeout=TIMEOUT)


@pytest.fixture(scope="session", autouse=True)
def server_is_up():
    try:
        requests.get(f"{BASE_URL}/public/careers", timeout=TIMEOUT)
    except requests.ConnectionError:
        pytest.exit(f"No hay servidor en {BASE_URL}. Ejecuta `php artisan serve` y vuelve a probar.", returncode=2)


@pytest.fixture(scope="session")
def anon() -> Api:
    return Api()


@pytest.fixture(scope="session")
def tokens() -> dict:
    result = {}
    for role, email in USERS.items():
        response = login(email)
        assert response.status_code == 200, f"No se pudo iniciar sesión como {role} ({email}): {response.text}"
        result[role] = response.json()["token"]
    return result


@pytest.fixture(scope="session")
def student(tokens) -> Api:
    return Api(tokens["student"])


@pytest.fixture(scope="session")
def admin(tokens) -> Api:
    return Api(tokens["admin"])


@pytest.fixture(scope="session")
def coordinator(tokens) -> Api:
    return Api(tokens["coordinator"])


@pytest.fixture(scope="session")
def company(tokens) -> Api:
    return Api(tokens["company"])
