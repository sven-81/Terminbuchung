# Terminbuchung API (MVP)

Minimales Backend für digitale Terminvergabe. Laravel 12 | PHP 8.4 | SQLite | Hexagonale Architektur

---

## Setup & Tests

```bash
# Starten
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate:fresh --seed

# Tests (120 Tests, alle grün)
./vendor/bin/sail test

# API-Dokumentation
open http://localhost/api/documentation
```

**Endpunkte:**
- `GET /api/consultants` - Liste aller Consultants
- `POST /api/bookings` - Termin buchen (Kernfunktion)

**Testdaten:** HTTP-Requests in `api-tests.http`

---

## Architektur-Entscheidungen

**Hexagonale Architektur** nach Tom Hombergs gewählt, um Geschäftsregeln zentral in der Domain zu halten statt über den Code verstreut. Lose Kopplung durch Port/Adapter-Pattern ermöglicht einfaches Austauschen von Infrastruktur-Komponenten.

```
Domain (Kern)
  ├── ValueObjects: Email, TimeSlot, CustomerName, DailyCapacity
  ├── Entities: Consultant, Booking
  └── Business Logic in Value Objects gekapselt

Application (Use Cases)
  ├── CreateBookingService
  └── Ports: LoadConsultantPort, SaveBookingPort

Adapter (Infrastruktur)
  ├── HTTP Controller
  └── Repositories (Query Builder statt Eloquent)
```

**Rich Domain Model:** Alle Value Objects sind immutable (readonly), validieren sich selbst und kapseln fachliches Verhalten (z.B. `TimeSlot::overlapsWith()`).

---

## Getroffene Annahmen

- **Consultants via Seeder**: MVP fokussiert Buchungsprozess, CRUD-Verwaltung kommt später
- **Keine Authentifizierung und Authorisierung**: Muss vor Produktion implementiert werden (siehe Risiken)
- **UTC-Zeitzone**: Vermeidet Komplexität bei Zeitumstellungen
- **Kunde = Name + E-Mail**: Kein User-Management im MVP
- **Kapazität in Minuten**: Flexibler als feste Slot-Anzahl

---

## Erwogene Alternativen

**Eloquent Models statt Domain Entities?** Verworfen, da zu starke Framework-Kopplung.
**Validierung nur im Controller?** Verworfen, würde Geschäftsregeln verstreuen. Value Objects validieren sich selbst.
**Event Sourcing?** Overengineering für MVP. CRUD-Persistierung reicht aktuell.

---

## Risiken

** Kritisch (vor Produktion):**
1. **Keine Authentifizierung, kein Rollenmanagement** - Jeder kann aktuell Bookings anlegen. Lösung: Basic Auth 
   implementieren.
2. **Race Conditions** - Gleichzeitige Buchungen nicht transaktional geprüft. Lösung: Pessimistic Locks.
3. **Fehlende Overlap-/Capacity-Checks** - Nur Format validiert, keine fachlichen Konflikte. Nächster Schritt.

---

## Nächste Schritte für Produktion

**Phase 1: Geschäftslogik vervollständigen**
- Overlap-Detection (DB-Query über bestehende Bookings)
- Capacity-Check (Summe Tagesminuten)
- Systemtests schreiben
- OpenAPI-Response-Validierung

**Phase 2: Sicherheit**
- Input Sanitization
- Basic Authentication
- Transaktionen mit DB-Locks

**Phase 3: Deployment**
- Environment-Config (Prod/Staging)
- Health-Check Endpoint
- Load-Tests

---

## KI-Tools Nutzung

**Tools:** Claude 3.5 Sonnet (OpenAPI-Optimierung und Code-Optimierung, Testdatenerzeugung, Dataprovider), PhpStorm AI 
(Autovervollständigung)

**Vorgehen:**
1. OpenAPI-Spec zuerst erstellt und mit Claude optimiert
2. Domain Layer → Application Layer → Adapter Layer
3. Tests mit Data Providern (von Claude optimiert, Zeitersparnis)

**Eigene Entscheidungen:**
- Hexagonale Architektur (nicht KI-vorgeschlagen)
- Rich Model Pattern
- Test-Strategie (Unit + Feature)
- Fehlerbehandlung (422 vs. 409)

**KI-generiert:** Boilerplate für Value Objects, Test Data Providers, Repository-Struktur

---

## Weitblick: Consultant-Verwaltung

Außerhalb MVP-Scope geplant: CRUD-Endpoints für Consultants (`POST/GET/PUT/DELETE /api/consultants`). Benötigt zusätzliche Services, E-Mail-Uniqueness-Check und Admin-Autorisierung.

---

**Status:** MVP abgeschlossen in ~3h | 120 Tests grün
