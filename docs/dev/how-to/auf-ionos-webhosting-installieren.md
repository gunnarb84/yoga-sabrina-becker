> **Typ:** How-To · **Für:** Entwickler · **Bezug:** [harness-php/_operations.md](../../../harness-php/_operations.md) §4.1b, `docs/dev/referenz/konfiguration.md`
> **Stand:** 2026-09-15

# Auf IONOS Webhosting installieren

So richten Sie das Produkt auf IONOS klassischem Webhosting (Shared Hosting) neu ein —
von der Datenbank bis zum Rauchtest. Das Vorgehen folgt `_operations.md` §4.1b (klassisches
Webhosting: PHP-FPM, kein Daemon, Cron für Scheduler und Warteschlange).

## Voraussetzungen

- IONOS-Webspace mit **SSH-Zugang** (im Panel unter „Sicherheit“ aktivieren).
- **PHP 8.4** als CLI-Version (im Panel unter „PHP-Einstellungen“ setzen).
- **MySQL 8** bzw. MariaDB 10.6+ — im Panel eine Datenbank anlegen und die Zugangsdaten
  notieren (Host, Datenbankname, Benutzer, Passwort).
- **Minütlicher Cron** verfügbar (IONOS bietet Cronjobs ab 1 Minute Abstand).
- Lokaler SSH-Zugang zum Server, Benutzername im Stil `su<Nummer>`:
  ```bash
  ssh su<Nummer>@<ihre-domain>.de
  ```

## 1. DocumentRoot auf `public/` legen

Im IONOS-Panel die Ziel-Adresse der Domain auf das Verzeichnis `public` unterhalb des
Projekts setzen (siehe Schritt 2 für die Ablage). Die `index.php` des Projekts liegt in
`src/public/` — der Webroot zeigt dorthin, **nicht** auf das Projektverzeichnis selbst.
Der Wechsel braucht einige Minuten, bis er greift.

## 2. Projekt klonen

Das Projekt liegt **außerhalb** des Webroots:

```bash
cd ~
git clone <REPOSITORY-URL> yoga
```

Danach zeigt der Webroot (Schritt 1) auf `~/yoga/src/public`. Nur `public/` ist vom Web
erreichbar; `.env`, `storage/` und der Code liegen außerhalb.

## 3. Schreibbare Verzeichnisse anlegen

Git legt leere Verzeichnisse nicht an — Laravel braucht sie zwingend:

```bash
mkdir -p ~/yoga/src/storage/framework/cache/data \
         ~/yoga/src/storage/framework/sessions \
         ~/yoga/src/storage/framework/views \
         ~/yoga/src/storage/logs \
         ~/yoga/src/bootstrap/cache
```

Fehlt eines dieser Verzeichnisse, bricht `composer install` beim `package:discover`-Skript
mit `Please provide a valid cache path` ab.

## 4. Composer installieren

Auf dem Webspace ist Composer meist nicht vorinstalliert. Composer in das Home-Verzeichnis
legen (nicht ins Projekt):

```bash
cd ~
php -r "copy('https://getcomposer.org/installer','composer-setup.php');"
php composer-setup.php
rm composer-setup.php
```

Danach liegt `~/composer.phar`. Alle Composer-Aufrufe erfolgen über diese Datei:

```bash
cd ~/yoga/src
php ~/composer.phar install --no-dev --optimize-autoloader
```

**Erfolgskriterium:** `ls -l vendor/composer/autoload_classmap.php` zeigt die Datei. Fehlt
sie (etwa weil ein früherer Lauf abgebrochen war), den Aufruf vollständig wiederholen und
notfalls `php ~/composer.phar dump-autoload --optimize` nachziehen. Ohne Classmap scheitert
später auch `php artisan tinker` mit einer Meldung zu `autoload_classmap.php`.

Die PSR-4-Warnung zu `Yoga\Modules\Verwaltung\Tests\TestFactory` ist harmlos — sie betrifft
nur die Test-Läufer, die ohne `--no-dev` gar nicht installiert werden.

## 5. `.env` anlegen

`src/.env` auf dem Server anlegen (nicht aus `.env.example` kopieren und vergessen, die
Pflichtwerte zu setzen). Geheimnisse liegen **nie** im Repository:

```ini
APP_NAME="Yoga Sabrina Becker"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://<ihre-domain>.de
APP_LOCALE=de

DB_CONNECTION=mysql
DB_HOST=<db-host>
DB_PORT=3306
DB_DATABASE=<db-name>
DB_USERNAME=<db-benutzer>
DB_PASSWORD=<db-passwort>

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=<smtp-host>
MAIL_PORT=587
MAIL_USERNAME=<smtp-benutzer>
MAIL_PASSWORD=<smtp-passwort>
MAIL_FROM_ADDRESS=info@<ihre-domain>.de
MAIL_FROM_NAME="Yoga Sabrina Becker"
MAIL_CONTACT_INQUIRY_RECIPIENT=info@<ihre-domain>.de

ADMIN_EMAIL=<e-mail-der-administratorin>
ADMIN_PASSWORD=<sicheres-passwort>

# Composer liegt wegen vendor-dir=../vendor auf Projektebene — nötig für php artisan tinker
COMPOSER_VENDOR_DIR=/home/www/yoga/vendor
```

`ADMIN_EMAIL` und `ADMIN_PASSWORD` sind Pflichtangaben je Installation: Das Seeding legt
das Konto der Administratorin damit an und verweigert ohne diese Werte den Lauf
(`docs/dev/referenz/konfiguration.md`). Der Demo-Datensatz aus `.env.example` gilt nur für
lokale Entwicklung — für den produktiven Zugang ein eigenes, starkes Passwort setzen.

## 6. Anwendung installieren

```bash
cd ~/yoga/src
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Das Seeding legt das Admin-Konto (Schritt 5), die Rechtstexte und die CMS-Inhalte an. Den
Demo-Seeder **nicht** ausführen — er erzeugt Testdaten, die in der produktiven Installation
nichts zu suchen haben.

## 7. Frontend-Assets bauen und hochladen

Die öffentliche Webseite lädt CSS/JS über Vite (`@vite`). Auf dem Webspace gibt es kein
Node — die Assets werden lokal gebaut und hochgeladen (im Container-Betrieb erledigt das
der Dockerfile). `src/public/build` ist git-ignoriert und wird nicht mitgepusht:

```bash
# lokal, im Projekt-Wurzelverzeichnis
cd src
npm install
npm run build
```

Hochladen (lokal ausführen; `<Nummer>` ist der IONOS-Benutzer):

```bash
cd ~/VS_CODE/yoga_sabrina_becker
tar czf - src/public/build \
  | ssh su<Nummer>@access-<server>.webspace-host.com 'tar xzf - -C /home/www/yoga'
```

**Erfolgskriterium:** `ls ~/yoga/src/public/build/manifest.json` auf dem Server zeigt die
Datei. Fehlt das Verzeichnis, zeigt die öffentliche Webseite einen 500er („Vite manifest
not found") — die Verwaltung funktioniert auch ohne, sie nutzt statisches CSS.

## 8. Cronjobs einrichten

Im IONOS-Panel zwei **minütliche** Cronjobs anlegen, beide mit PHP 8.4:

1. **Scheduler** (geplante Aufgaben):
   ```bash
   /usr/bin/php8.4 /home/www/yoga/src/artisan schedule:run >> /dev/null 2>&1
   ```
2. **Warteschlange** (E-Mail-Versand u. ä.): Auf dem Webhosting läuft kein Daemon — der
   Worker wird je Lauf gestartet und beendet sich selbst:
   ```bash
   /usr/bin/php8.4 /home/www/yoga/src/artisan queue:work --stop-when-empty --max-time=50 >> /dev/null 2>&1
   ```

Der exakte PHP-Pfad kann je Server abweichen; mit `which php` bzw. `php -v` in der
SSH-Sitzung prüfen und den Pfad aus dem Cronjob-Beispiel des Panels übernehmen.

## 9. HTTPS sicherstellen

Im IONOS-Panel das SSL-Zertifikat aktivieren (kostenloses Let’s-Encrypt-Zertifikat
reicht) und die Domain dauerhaft auf HTTPS umleiten. `APP_URL` aus Schritt 5 muss mit der
HTTPS-Adresse übereinstimmen, sonst erzeugen erzeugte Links und Beleg-PDFs falsche URLs.

## 10. Rauchtest

- `https://<ihre-domain>.de` zeigt die Webseite mit Kursen und Kontaktformular.
- `https://<ihre-domain>.de/verwaltung` — Anmeldung mit dem Admin-Konto aus Schritt 5
  funktioniert.
- Eine Testanmeldung über das Kontaktformular erzeugt eine Benachrichtigungs-E-Mail.
- `php artisan about` in der SSH-Sitzung zeigt keine fehlenden Verzeichnisse und
  `APP_ENV=production`.

Bei Fehlern zuerst `~/yoga/src/storage/logs/laravel.log` prüfen.

## Updates ausrollen

Für spätere Aktualisierungen der Installation:

```bash
cd ~/yoga/src
git pull
php ~/composer.phar install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

Hat sich die Webseite (Blade, CSS, JS) geändert, zusätzlich Schritt 7 wiederholen —
sonst läuft die Seite weiter mit den alten Assets oder bricht mit „Vite manifest not
found" ab, falls `public/build` neu angelegt werden muss.

Ein abgebrochener Schritt ist **vollständig zu wiederholen** — nicht nur der scheinbar
fehlgeschlagene Teil. Das Rückkehrverfahren und die Ablaufregelung stehen in
`_operations.md` §5.

## Stolpersteine

| Symptom | Ursache | Lösung |
|---|---|---|
| `could not open input file: composer.phar` | Composer fehlt auf dem Webspace | Schritt 4: Installer per `php -r "copy(...)"` laden |
| `Please provide a valid cache path` bei `package:discover` | Leere Framework-Verzeichnisse fehlen (Git legt sie nicht an) | Schritt 3 wiederholen, danach Composer-Lauf komplett neu |
| `php artisan tinker` bricht mit Meldung zu `autoload_classmap.php` ab | Composer-Install unvollständig **oder** `COMPOSER_VENDOR_DIR` fehlt (Tinker sucht sonst unterhalb von `src/`) | `ls ~/yoga/vendor/composer/autoload_classmap.php` prüfen; fehlt sie, Schritt 4 wiederholen — liegt sie dort, `COMPOSER_VENDOR_DIR=/home/www/yoga/vendor` in die `.env` setzen |
| 500er beim ersten Aufruf | `.env` unvollständig oder Caches veraltet | `storage/logs/laravel.log` prüfen, Schritt 6 wiederholen |
| `Vite manifest not found` im Log, Webseite zeigt 500er | `src/public/build` fehlt auf dem Server (git-ignoriert, kein Node auf dem Webspace) | Schritt 7: Assets lokal bauen und hochladen |

## Siehe auch

- [Konfiguration und Umgebung](../referenz/konfiguration.md)
- [Build und Ausrollen](../referenz/build-und-ausrollen.md)
- [Tests ausführen](tests-ausfuehren.md)
- [harness-php/_operations.md](../../../harness-php/_operations.md) — Betriebsformen, Aktualisierung, Sicherung