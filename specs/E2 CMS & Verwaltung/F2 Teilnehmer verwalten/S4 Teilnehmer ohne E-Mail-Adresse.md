# Teilnehmer ohne E-Mail-Adresse

## Meta
- **State:** Implemented

## User Story
Als Administratorin möchte ich Teilnehmer/innen ohne E-Mail-Adresse führen, damit ich auch
Laufkundschaft ohne E-Mail-Adresse erfassen kann.

## Description
Die E-Mail-Adresse eines Teilnehmers/einer Teilnehmerin ist optional. Im
Online-Anmeldeprozess der Webseite bleibt sie Pflicht, weil die Anmeldebestätigung und die
Barquittung per E-Mail versendet werden. Wird eine Teilnehmerin/ein Teilnehmer ohne
E-Mail-Adresse angelegt (z. B. über die Bareinnahme ohne Anmeldung, siehe F4-S8), erhält
sie/er keine E-Mails.

## Akzeptanzkriterien
- Eine Teilnehmerin/ein Teilnehmer kann ohne `email` angelegt werden.
- Das Feld `email` einer Teilnehmerin/eines Teilnehmers kann leer bleiben; ein vorhandener
  Wert entspricht dem Format einer E-Mail-Adresse.
- Der Teilnehmerliste wird angezeigt, wenn keine E-Mail-Adresse vorhanden ist.
- Die Online-Anmeldung über die Webseite verlangt weiterhin eine gültige E-Mail-Adresse
  (siehe E1 F3).