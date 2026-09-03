> **Typ:** Erklärung · **Für:** Entwickler · **Bezug:** [harness-php/_architecture.md](../../../harness-php/_architecture.md), [harness-php/_design.md](../../../harness-php/_design.md)
> **Stand:** 2026-09-03

# Architekturübersicht

Das Projekt ist ein **modularer Monolith**: ein einziges deploybares Artefakt, in dem die Fachlichkeit aber in klar abgegrenzte Module gegliedert ist.

## Produkte und Module

- **Webseite** (`modules/Webseite`): Öffentliche Kurs-, Event- und Workshop-Darstellung, Online-Anmeldung, Warteliste.
- **Verwaltung** (`modules/Verwaltung`): CMS für die Webseite, Verwaltung von Veranstaltungen, Teilnehmer/innen, Anmeldungen, Zahlungen und Belegen.
- **Platform** (`platform/`): Plattformbausteine, die von beiden Modulen genutzt werden, z. B. Identität, Nummernsequenzen, gemeinsame Wertobjekte.

## Schichten pro Modul

Jedes Modul gliedert sich in vier Schichten:

| Schicht | Verantwortung |
|---|---|
| `Domain` | Fachliche Entitäten, Value Objects, Domänen-Ereignisse, Regeln |
| `Application` | Vorgänge (Use Cases) als einzelne Klassen mit `execute(Request): Result<Response>` |
| `Persistence` | Eloquent-Modelle, Migrationen, Repositories, Query-Objekte |
| `Ui` | Livewire-Komponenten, Controller, Blade-Views und Präsentations-DTOs |

## Wichtige Konventionen

- Jeder Vorgang ist eine Klasse mit genau einer öffentlichen Methode `execute()`.
- Vorgänge geben ein `Result<Response>` zurück (`Success<T>` / `Failure`).
- Primärschlüssel sind **binäre UUID v7** (`binary(16)`), außen als String dargestellt (siehe [ADR-0002](entscheidungen/0002-binaere-uuid-v7.md)).
- Belegnummern sind fortlaufend und werden über `platform/NumberSequence` erzeugt.
- Geschäftslogik liegt in `Domain`/`Application`, nie in Livewire-UI.

## Siehe auch

- [Module und Schichten](referenz/module.md)
- [Datenmodell](referenz/datenmodell.md)
- [ADR-0001: Modulgrenzen und Plattformschicht](entscheidungen/0001-modulgrenzen-und-plattformschicht.md)
- [ADR-0002: Binäre UUID v7 als Primärschlüssel](entscheidungen/0002-binaere-uuid-v7.md)
