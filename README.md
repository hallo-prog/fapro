<div align="center">

# FaPro – Angebots-, Auftrags- & Rechnungsplattform

<strong>Moderne Symfony 6.4 Anwendung für Angebotserstellung, Projekt- & Kundenkommunikation, Dokumente (PDF), Push Notifications & Auswertungen.</strong>

</div>

---

## Inhaltsverzeichnis
1. Überblick
2. Haupt-Features
3. Technologie-Stack
4. Schnellstart (TL;DR)
5. Systemvoraussetzungen
6. Installation (Detail)
7. Konfiguration / Environment Variablen
8. Datenbank & Migrationen
9. Build & Assets (Webpack Encore)
10. Tests & Code-Qualität
11. Push Notifications (VAPID)
12. Deployment (Kurzleitfaden)
13. Troubleshooting
14. Architektur & Verzeichnisstruktur
15. Sicherheit / Secrets
16. Beitrag / Contribution
17. Lizenz / Rechtliches Hinweisfeld

---

## 1. Überblick
FaPro digitalisiert wiederkehrende Angebots-, Auftrags- und Rechnungsprozesse. Kernpunkte: strukturierte Stammdaten, flexible Angebots-/Positionslogik, PDF-Generierung, Kalender-/Terminverwaltung, Benachrichtigungen (E-Mail & Web Push) sowie Reporting.

## 2. Haupt-Features
- Angebot / Auftrag / Rechnung Lebenszyklus
- PDF-Erzeugung (FPDF/FPDI, Barcode & QR Codes)
- Kunden- & Benutzerverwaltung (Rollen, Rechte – Symfony Security)
- Kalender & Termine (tattali/calendar-bundle)
- Bildverarbeitung (intervention/image, CropperJS Integration)
- Push Notifications (Web Push, VAPID)
- Echtzeitnahe Kommunikation via Messenger (Doctrine Transport / optional weitere Transports)
- Mehrsprachigkeit vorbereitet (Translation Komponenten)
- Frontend Tooling via Stimulus + Webpack Encore
- JSON-Kontexte für flexible Erweiterung (utf8mb4 + JSON Felder)

## 3. Technologie-Stack
| Bereich | Technologie |
|--------|-------------|
| Framework | Symfony 6.4 LTS |
| Sprache | PHP 8.3 (min 8.1) |
| DB | MariaDB 10.11 (MySQL kompatibel) |
| ORM / DBAL | Doctrine ORM / Migrations |
| Frontend | Webpack Encore (Stimulus, SCSS, optional Chart.js, CropperJS) |
| Queue / Async | Symfony Messenger (Default Doctrine Transport) |
| PDF/Barcode | FPDF/FPDI, endroid/qr-code, picqer/barcode |
| Push | Web Push (minishlink/web-push) |
| Tests | PHPUnit, DAMA Doctrine Test Bundle |
| Analyse / Refactor | PHPStan, Rector, PHP-CS-Fixer |

## 4. Schnellstart (TL;DR)
```bash
git clone <REPO_URL> fapro && cd fapro
docker compose up -d database
cp .env .env.local   # danach anpassen (DB, VAPID, etc.)
php composer.phar install
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction
npm install && npm run dev   # optional für Assets
php -S 0.0.0.0:8000 -t public public/index.php
```
App erreichbar unter: http://localhost:8000

## 5. Systemvoraussetzungen
- PHP >= 8.1 (empfohlen 8.3) mit Extensions: intl, json, pdo_mysql, mbstring, zip, gd, openssl
- Node.js (>=18) & npm (für Asset Build)
- Docker (für MariaDB & optionale Tools wie Mailpit)
- Composer (lokal oder via `composer.phar` im Repo)

## 6. Installation (Detail)
1. Repository klonen (siehe Schnellstart)
2. MariaDB Container starten: `docker compose up -d database`
3. `.env.local` anlegen (siehe Abschnitt 7)
4. Abhängigkeiten: `php composer.phar install`
5. DB erstellen & Migrationen: `php bin/console doctrine:migrations:migrate`
6. Dev Server: `php -S 0.0.0.0:8000 -t public public/index.php`
7. Assets optional: `npm run dev --watch`

## 7. Konfiguration / Environment Variablen
Leg projektspezifische Werte ausschließlich in `.env.local` ab (wird nicht committed). Wichtige Variablen:

| Variable | Pflicht | Beschreibung |
|----------|---------|--------------|
| APP_ENV | ja | Umgebung (dev, prod, test) |
| APP_DEBUG | dev: ja | Debug-Modus (0/1) |
| APP_SECRET | ja | Random Hex Token für CSRF/Signer |
| APP_LOCALE | optional | Standard-Locale (z.B. de) |
| APP_SUBDOMAIN | optional | Mandanten-/Kontextsteuerung |
| APP_URL | empfohlen | Basis-URL (z.B. http://localhost:8000) |
| DATABASE_URL | ja | MariaDB DSN (utf8mb4) |
| MESSENGER_TRANSPORT_DSN | ja | Doctrine oder andere Transport-DSN |
| VAPID_PUBLIC_KEY | bei Push | Öffentlicher WebPush Schlüssel |
| VAPID_PRIVATE_KEY | bei Push | Privater WebPush Schlüssel |
| MAILER_DSN | optional | Mailer Transport (Mailpit z.B. smtp://mailer:1025) |

Beispiel `.env.local`:
```dotenv
APP_ENV=dev
APP_DEBUG=1
APP_URL=http://localhost:8000
APP_LOCALE=de
DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=10.11&charset=utf8mb4"
MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0
VAPID_PUBLIC_KEY=<public>
VAPID_PRIVATE_KEY=<private>
```

## 8. Datenbank & Migrationen
- DB Standard: MariaDB (MySQL Dialekt). PostgreSQL wurde entfernt, da Migrationen MySQL-spezifisch (AUTO_INCREMENT, ENGINE, utf8mb4).
- Migration erstellen: `php bin/console make:migration`
- Anwenden: `php bin/console doctrine:migrations:migrate`
- Rollback (einzelne Version): `php bin/console doctrine:migrations:migrate <version>`

Hinweis zu reservierten Wörtern: Die Spalte `keys` wurde in Migration nach `subscription_keys` umbenannt (vermeidet SQL Fehler). Nutze bei Bedarf Backticks oder gleich sprechende Namen.

Reset (Achtung: Datenverlust):
```bash
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate --no-interaction
```

## 9. Build & Assets (Webpack Encore)
```bash
npm install
npm run dev        # Development Build
npm run watch      # Watch Mode
npm run build      # Production (minified)
```
Output liegt unter `public/build/` (wird via .gitignore ausgeschlossen).

## 10. Tests & Code-Qualität
```bash
php bin/phpunit                # Unit/Feature Tests
vendor/bin/phpstan analyse     # Statische Analyse
vendor/bin/rector process      # Automatische Refactorings
vendor/bin/php-cs-fixer fix    # Coding Style
```
Empfehlung: Vor Commit mind. PHPUnit & PHPStan laufen lassen.

## 11. Push Notifications (VAPID)
VAPID Schlüssel generieren:
```bash
vendor/bin/web-push generate:vapid
```
Dann Werte in `.env.local` übernehmen. Öffentlichen Key im Frontend verwenden, privaten nur serverseitig.

## 12. Deployment (Kurzleitfaden)
```bash
composer install --no-dev --optimize-autoloader
php bin/console cache:clear --env=prod
php bin/console doctrine:migrations:migrate --no-interaction --env=prod
npm ci && npm run build
```
Stelle sicher, dass `APP_ENV=prod` und `APP_DEBUG=0`. Webserver (nginx/Apache) auf `public/` zeigen lassen.

## 13. Troubleshooting
| Problem | Ursache | Lösung |
|---------|---------|--------|
| could not find driver | pdo_mysql fehlt | PHP Extension nachinstallieren (php8.3-mysql) |
| Syntax error near AUTO_INCREMENT | Falscher DB Dialekt (PostgreSQL) | Auf MariaDB wechseln oder Migration anpassen |
| Table already exists | Migration erneut über bereits erzeugte Tabelle | Tabelle droppen oder Version anpassen |
| Cache Fehler env var not found | Fehlende Variable | In `.env.local` ergänzen & `cache:clear` |
| Push funktioniert nicht | VAPID Keys fehlen oder falsch | Neue Keys generieren & `.env.local` aktualisieren |

Logs: `var/log/dev.log` (nicht committen).

## 14. Architektur & Verzeichnisstruktur (Auszug)
| Ordner | Zweck |
|--------|-------|
| `src/` | Anwendungscode (Controller, Entity, Services) |
| `migrations/` | Doctrine Migrationen |
| `templates/` | Twig Templates |
| `assets/` | JS/SCSS/Stimulus Controller |
| `public/` | Webroot (index.php, Assets, Bilder) |
| `var/` | Cache, Logs (lokal) |

### 14.1 Domain Modell (vereinfachter Überblick)

Hauptaggregate & Beziehungen (vereinfacht, nicht vollständig):

```
User 1—* Offer 1—1 OfferOption
User 1—* Inquiry
User 1—* Invoice *—1 Order *—1 Customer
Customer 1—* Offer *—* ProjectTeam (ManyToMany via ProjectTeam.offers / users)
Customer 1—* Invoice 1—* Reminder
Offer 1—* ActionLog (chronologischer Verlauf)
Offer 1—1 Order 1—* ProductOrder *—1 Product
Offer 1—* OfferItem / OfferAnswers (Konfigurations-Bausteine)
PushSubscription *—1 User
```

Zentrale Entitäten (Auswahl) & Rolle:
- `User`: Authentifizierung, Rollen, Notifications (Push, Slack optional) – besitzt Invoices, Inquiries, Timesheets.
- `Customer`: Geschäftskunde/Endkunde – verknüpft mit Offers, Invoices, ActionLogs.
- `Offer`: Kernobjekt für Angebotserstellung; enthält Kontext (JSON), Status-Felder, Preis-/Produktverweise.
- `Order`: Folgeobjekt nach Angebotsannahme; Basis für Invoices.
- `Invoice`: Abrechnung mit Positionen (posX Felder), Zahlungstracking, Mahnwesen via `Reminder`.
- `ActionLog`: Chronologisches Journal (Statuswechsel, Aktivitäten, Kommunikation) – strukturierte Typen (`TYPE_CHOICES`).
- `Product` & Kategorien (`ProductCategory`, `ProductSubCategory`): Katalogstruktur.
- `ProjectTeam`: Team-/Ressourcengruppen, ordnet Users und Offers zu.
- `PushSubscription`: Web Push Endpoint (subscription_keys) für Browser-Notifications.

### 14.2 Technische Patterns
- Verwendung von JSON Feldern (`context`, `roles`, `subscription_keys`) für flexible Schema-Erweiterung ohne invasive Migration.
- Weitgehende Nutzung von Doctrine Collections & bidirektionalen Beziehungen (Achtung auf potentielle N+1 Queries – ggf. FetchJoins einsetzen).
- Indexierung strategischer Spalten (z.B. `Offer.status`, `Offer.number`, `Invoice.date`) zur Beschleunigung typischer Filter.
- Service Layer (nicht vollständig gezeigt) vermutlich unter `src/Service/` (Erweiterungspunkt: Caching, externe Integrationen, Mail, Slack, Push).

### 14.3 Workflows (High-Level)
Angebotsprozess:
1. Inquiry (Anfrage) erfasst
2. Offer erstellt (initialer Status / Kontext)
3. Optionale ActionLogs (Material fehlt, Notizen, Telefon, gesendet)
4. Angebot angenommen → Order erzeugt
5. Order → Invoice(n) generiert
6. Reminder (Mahnung) bei fehlender Zahlung

Push Notification Flow:
1. Browser registriert Service Worker & sendet Subscription an Backend (`PushSubscription`)
2. Backend speichert `subscription_keys`
3. Ereignis (z.B. neue Nachricht / Statuswechsel) → Service erzeugt WebPush Nachricht
4. Versand über `minishlink/web-push` mit VAPID Keys

### 14.4 Skalierung / Performance Überlegungen
- Caching Layer (Symfony Cache / Redis) einführbar für häufige Lese-Queries (Offers, Products).
- Messenger kann für asynchrone Tasks (PDF-Erzeugung, größere E-Mail Batches, Push Versand) erweitert werden.
- DB Sharding nicht nötig initial; sinnvolle Indizes erweitern (z.B. kombinierte Indexe für häufige Dashboard Filter: status_date + status).
- Asset Bundling Production Mode (`npm run build`) liefert Tree Shaking & Minimierung.

### 14.5 Erweiterungs-Ideen
- Audit Trail via Listener für kritische Entitäten (Offer, Invoice)
- Soft Deletes (Timestamp) statt physischem Löschen (aktuell `deleteIt` Flag bei Offer – könnte vereinheitlicht werden)
- Mehrstufige Angebotsfreigabe (Approval Workflow) per State Machine (`symfony/workflow` Bundle)
- Volltextsuche (Elasticsearch / OpenSearch) auf Offer/Customer Notizen
- API Layer (API Platform) für externe Integrationen
- Frontend Modernisierung (Inertia.js / Vue / React) falls mehr Interaktivität nötig

### 14.6 Qualitäts-Gates Empfehlung
In CI Pipeline integrieren:
```bash
php -d memory_limit=-1 vendor/bin/phpstan analyse --memory-limit=1G
vendor/bin/php-cs-fixer fix --dry-run --diff
vendor/bin/rector process --dry-run
php bin/phpunit --testdox
```

### 14.7 Security Hinweise
- Passwörter: Nutzen Symfony Password Hasher (nicht plain speichern). Prüfen ob Migrations alte Hashverfahren enthalten.
- CSRF & SameSite Cookies aktiv halten; `APP_SECRET` regelmäßig rotieren (mit Rolling Strategy).
- Rate Limiting für Login-Endpunkte (Symfony RateLimiter Component ergänzen).
- Content Security Policy Header via EventSubscriber setzen.

### 14.8 Observability
- Aktivierung von `monolog` Kanal-Routing (separate Kanäle für Doctrine Slow Queries, Push, Mail).
- Optionale Integration: Sentry für Exceptions, OpenTelemetry für Traces.


## 15. Sicherheit / Secrets
- Keine echten Secrets committen (`.env.local` ist in `.gitignore`).
- Für Produktion: Env Variablen über Server / Orchestrierung setzen.
- Option: `composer dump-env prod` für Build-Zustand.

## 16. Beitrag / Contribution
Pull Requests & Issues willkommen. Bitte vor größeren Änderungen ein kurzes Konzept vorschlagen (Issue eröffnen) und Code-Style + Tests beachten.

## 17. Lizenz / Rechtliches Hinweisfeld
Keine explizite Lizenzdatei gefunden – bitte vor externer Nutzung klären oder LICENSE hinzufügen.

---

Made with Symfony ❤️


