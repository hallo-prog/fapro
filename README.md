# FaPro Software
[Demo and Documentation](https://kundenservice.zukunftsorientierte-energie.de/)

Angebots- und Rechnungserstellungs-Software Installationsanleitung

---

# Installation

Diese Anleitung beschreibt die Schritte zur Installation der FaPro Software, die auf Symfony 6.4 basiert. Stellen Sie sicher, dass die folgenden Voraussetzungen erfüllt sind, bevor Sie beginnen.

## Voraussetzungen
- **PHP**: Version 8.1 oder höher
- **Composer**: Aktuelle Version installiert
- **Datenbank**: MariaDb Datenbank
- **Git**: Zum Klonen des Repositories
- **Symfony**: Version 6.4


## Installationsschritte

1. **Repository klonen**
   Klone das Repository von GitHub auf deinen lokalen Rechner:
   ```bash
   git clone git@github.com:dein_username/fapro-software.git
   cd fapro-software
2. **Docker Datenbank starten**
   ```bash
   docker compose up -d database
   ```

3. **Environment Variablen setzen**
   Erstelle `.env.local` (wird nicht committed) mit z.B.:
   ```bash
   APP_ENV=dev
   APP_DEBUG=1
   DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=10.11&charset=utf8mb4"
   VAPID_PUBLIC_KEY=<dein_public_key>
   VAPID_PRIVATE_KEY=<dein_private_key>
   ```
   VAPID Keys kannst du mit `vendor/bin/web-push generate:vapid` erzeugen oder aus `.vapid-keys.txt` übernehmen.

4. **Abhängigkeiten installieren**
   ```bash
   php composer.phar install
   ```

5. **Datenbank Migrieren**
   ```bash
   php bin/console doctrine:database:create --if-not-exists
   php bin/console doctrine:migrations:migrate --no-interaction
   ```

6. **Dev Server starten**
   ```bash
   php -S 0.0.0.0:8000 -t public public/index.php
   ```
   Applikation: http://localhost:8000

7. **Assets (optional)**
   ```bash
   npm install
   npm run dev
   ```

## Hinweise
* PostgreSQL Support wurde entfernt; Migrationen sind MySQL/MariaDB-spezifisch (AUTO_INCREMENT, utf8mb4, JSON Spalten).
* Reservierte Wörter (z.B. `keys`) nicht direkt als Spaltennamen verwenden; nutze stattdessen Umbenennungen (z.B. `subscription_keys`).
* `.env` wird versioniert und enthält nur generische Defaults. Projekt-/geheime Werte gehören in `.env.local`.

