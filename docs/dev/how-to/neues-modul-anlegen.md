> **Typ:** How-To · **Für:** Entwickler · **Bezug:** [harness-php/_design.md](../../../harness-php/_design.md), [PROJEKT.md](../../../PROJEKT.md)
> **Stand:** 2026-09-03

# Ein neues Modul anlegen

1. **Verzeichnisstruktur anlegen**
   ```
   modules/{Modul}/
   ├── Application/
   ├── Domain/
   ├── Persistence/
   └── Ui/
   ```

2. **Composer-Autoloading ergänzen**
   In `src/composer.json` unter `autoload.psr-4` und `autoload-dev.psr-4` einen Eintrag für das neue Modul hinzufügen:
   ```json
   "Yoga\\Modules\\{Modul}\\": "../modules/{Modul}/",
   "Yoga\\Modules\\{Modul}\\Tests\\": "../modules/{Modul}/tests/"
   ```

3. **Service-Provider anlegen**
   - Modul-Provider im Namespace `Yoga\Modules\{Modul}\Ui\{Modul}ServiceProvider`.
   - In `src/bootstrap/providers.php` registrieren.

4. **Migrationen**
   - Migrationen liegen unter `modules/{Modul}/Persistence/Migrations/`.
   - Tabellenname mit Modul-Präfix, z. B. `webseite_activities`.

5. **Deptrac anpassen**
   - `deptrac.yaml` erweitern, falls neue Module die erlaubten Abhängigkeiten haben.
   - `analyse_all.py` ausführen, um Schichtverletzungen zu erkennen.

## Siehe auch

- [Module und Schichten](../referenz/module.md)
- [Architekturübersicht](../architektur.md)
