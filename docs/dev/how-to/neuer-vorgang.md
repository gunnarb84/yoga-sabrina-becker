> **Typ:** How-To · **Für:** Entwickler · **Bezug:** [harness-php/_design.md](../../../harness-php/_design.md)
> **Stand:** 2026-09-03

# Einen neuen Vorgang anlegen

Jeder fachliche Use Case ist ein Vorgang in der `Application`-Schicht.

## Schritte

1. **Request-DTO anlegen**
   - `readonly` Klasse im Namespace `Yoga\Modules\{Modul}\Application\{Gegenstand}`.
   - Pflichtfelder über Konstruktor-Property-Promotion.
   - Keine Eloquent-Models hier.

2. **Response-DTO anlegen**
   - `readonly` Klasse, die nur das zurückgibt, was der Aufrufer braucht.

3. **Vorgangsklasse anlegen**
   - Eine Klasse mit `public function execute(Request): Result<Response>`.
   - Abhängigkeiten über Konstruktor injizieren.
   - Validierung, fachliche Prüfung, Persistierung.
   - Fehler als `Failure` mit stabilem Fehlercode.

4. **Aus der UI aufrufen**
   - Livewire-Komponente oder Controller instanziiert den Vorgang und ruft `execute()` auf.
   - Auf `Success` oder `Failure` reagieren und dem Benutzer Meldungen anzeigen.

5. **Test schreiben**
   - Pest-Test im Modul- oder Feature-Testverzeichnis, abgeleitet aus den Akzeptanzkriterien.

## Beispiel

```php
namespace Yoga\Modules\Webseite\Application\Registration;

use Yoga\Platform\Shared\Application\Result;

final readonly class RegisterRequest
{
    public function __construct(
        public string $activityId,
        public string $firstName,
        public string $lastName,
        public string $email,
        public string $paymentType,
    ) {}
}

final readonly class RegisterResponse
{
    public function __construct(
        public string $registrationId,
        public string $status,
    ) {}
}

final class Register
{
    public function execute(RegisterRequest $request): Result
    {
        // fachliche Prüfung, Persistierung, Ergebnis
    }
}
```

## Siehe auch

- [Datenmodell](../referenz/datenmodell.md)
- [Tests ausführen](tests-ausfuehren.md)
