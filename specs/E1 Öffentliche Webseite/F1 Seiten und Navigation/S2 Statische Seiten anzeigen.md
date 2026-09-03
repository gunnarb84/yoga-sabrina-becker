# Statische Seiten anzeigen

## Meta
- **State:** Implemented

## User Story
Als Besucher möchte ich Impressum, AGB und Datenschutz einsehen können, damit ich die
rechtlichen Informationen und Kursbedingungen nachlesen kann.

## Description
Statische Seiten (`Page`) werden im CMS gepflegt und sind öffentlich erreichbar. Sie enthalten
formatierten Text. Im Ordner `Vorlagen` liegen bereits vorläufige Inhalte für AGB,
Datenschutzbestimmungen und Impressum, die als Ausgangsbasis dienen.

## Akzeptanzkriterien
- Jede veröffentlichte Seite (`Page`) ist unter ihrem `slug` erreichbar, z. B. `/impressum`,
  `/agb`, `/datenschutz`.
- Die Seite zeigt den `title` und den formatierten `content` an.
- Die Meta-Beschreibung (`metaDescription`) wird im HTML-Head ausgegeben.
- Ist eine Seite nicht veröffentlicht (`isPublished` = false), wird sie nicht öffentlich
  angezeigt und eine Fehlerseite mit dem Hinweis „Seite nicht verfügbar" angezeigt.
- Links zu den statischen Seiten erscheinen in der Navigation und im Fußbereich.
