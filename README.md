# Sven Duge

#todo adapt
#### User Management-Api Example

Beispiel einer RESTful API zur Verwaltung von Benutzern mit Authentifizierung und rollenbasierter Zugriffskontrolle.

**Technologien:** Laravel 11, PHP 8.4, MySQL 8.0, Docker, OpenAPI 3.0, Swagger UI, Laravel Sail

**OpenAPI Dokumentation:**
http://localhost:8080/api/documentation

---

## Run App

- Makefile im Root erstellt mit `install-vendor` Target für Docker-basierte Composer-Installation
- Laravel-Container mit Sail hochfahren mit `make up`
- Swagger UI aufrufen unter http://localhost:8080/api/documentation
- Datenbank migrieren mit `make fresh`
- Testdaten einfügen mit `make testdata`
- Manuell testen in der IDE: mit user-management-api/tests/http/users.http
- Unit und System Tests ausführen mit `make test`
- PHPStan Static Analysis mit `make phpstan`

Oder alles in einem Schritt mit `make show`

---

## Features

- Benutzer-CRUD (Create, Read, Update, Deactivate)
- Token-basierte Authentifizierung (Laravel Sanctum)
- Rollenbasierte Zugriffskontrolle (Admin/User)
- Soft-Delete (Deaktivierung statt Löschung)
- OpenAPI/Swagger Dokumentation
- Umfassende Tests (System, Unit)
- Docker-Support mit Laravel Sail
- PHPStan Static Analysis

---

## API-Endpunkte (Beispiele)

- `POST   /api/v1/auth/login`   – Login (Token erhalten)
- `GET    /api/v1/users`        – Alle User (Admin only)
- `GET    /api/v1/users/{id}`   – User abrufen
- `POST   /api/v1/users`        – User anlegen (Admin only)
- `PUT    /api/v1/users/{id}`   – User aktualisieren
- `DELETE /api/v1/users/{id}`   – User deaktivieren (Admin only)

---

## Standard-Benutzer

| Rolle | Email        | Passwort |
|-------|--------------|----------|
| Admin | admin@foo.de | secret   |
| User  | user@foo.de  | secret   |

---

## Architekturentscheidungen

- **Sanctum statt JWT:** Einfachere Token-Verwaltung, Standard für Laravel
- **Policy-basierte Authorization:** Saubere Trennung, automatische Prüfung
- **RESTful API Design:** Standard-HTTP-Methoden & Statuscodes
- **keine hexagonale Architektur:** Projekt ist zu klein, Overhead zu groß, Refactoring möglich

---

## Code-Qualität

- PSR-12 Coding Standard
- SOLID Prinzipien
- Type Hints überall
- PHPStan Static Analysis (`make phpstan`)

---

## Out of Scope

- Zeitlich nicht geschafft: OpenApi-Validierung in System Tests einzubauen
- Zeitlich nicht geschafft: Such- und Filtermöglichkeiten
