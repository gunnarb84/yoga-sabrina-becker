# Stornierungen mit Belegen erfassen

## Meta
- **State:** Implemented

## User Story
Als Administratorin möchte ich bei einer Stornierung Rückzahlungen ordentlich belegen können,
damit die Bareinnahmenliste und die Rechnungsliste korrekt bleiben.

## Description
Wurde bereits gezahlt, erzeugt eine Stornierung eine Gutschrift (bei Überweisung) oder eine
Rückgabebestätigung (bei Barzahlung). Beide haben einen eigenen lückenlosen Nummernkreis.

## Akzeptanzkriterien
- Bei Stornierung einer Anmeldung mit Zahlungsart „Überweisung" wird eine `CreditNote` mit
  der nächsten freien Nummer im Format `G-YYYY-NNNNN` erzeugt.
- Bei Stornierung einer Anmeldung mit Zahlungsart „Bar" wird eine `CashReturn` mit der
  nächsten freien Nummer im Format `RB-YYYY-NNNNN` erzeugt.
- Beide Belege verweisen auf die ursprüngliche Rechnung bzw. den ursprünglichen
  Bareinnahmenbeleg.
- Die Belege sind unveränderlich und können als PDF heruntergeladen werden.
- Die Listen für Gutschriften und Rückgabebestätigungen sind getrennt von den
  Hauptbeleglisten erreichbar.
