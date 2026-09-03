# Yoga Webseite mit CMS und angegliederter Verwaltung

Das Ziel ist eine Webseite für das Yoga Kurse, Events und Workshops einer Kleinunternehmerin.
An die Webseite soll ein Verwaltungssystem angegliedert sein, über das sich zum einen Inhalte für die Webseite erstellen lassen sollen.
Neue Kurse und andere Inhalte sollen im CMS erfasst werden können und auf der Webseite im entsprechende Bereich angezeigt werden.
Außerdem soll das Backend eine Verwaltung von Kursen, Events und Workshop, von Teilnehmern, von Barzahlungen und Überweisungen beinhalten

1. Webseite
- Für das Design der Webseite gibt es bereits einen Entwurf unter homepage-entwurf.html. Das ist nur ein grober Entwurf, die spätere Webseite soll kein One-Pager sein, sondern pro Rubrik eine eigene Seite bereistellen.
- Auf der Webseite soll es möglich sein sich für die Kurse anzumelden. Dafür soll keine Anmeldung nötig sein, die Eingabe der erforderlichen Daten und der E-Mail soll dafür ausreichen. Die Daten werden im Backend gespeichert.
- Im Ordner assets findest du bereits CSS-Dateien für die Gestalung und Logo.svg für die Webseite.
- Im Ordner Vorlagen findest du die Vorläufigen AGBs, die Datenschutzbestimmungen und das Impressum für die Webseite.

2. CMS für die Webseite
- Hier sollen neue Kurse, Events und Workshops angelegt werden können. Als Umfang für das erstellen sollte der bekannte Umfang von CMS verwendet werden, also Texte formatieren, Beiträge freischalten, sperren, Bilder oder Datei dazu hochladen können etc.

3. Integrierte Verwaltungsfunktionen
- CMS und die Verwaltung sollen nicht getrennt voneinander sein, es gibt nur ein Backend.
- Bei der Verwaltung benötige ich eine Teilnehmerverwaltung. Dort sollen Name und Adresse festgehalten werden, außerdem optional auch Gesundheitsinformationen, die für die Kurse wichtig sind. Teilnehmer melden sich über die Webseite an, meldet sich ein Teilnehmer mehrfach an, soll der Datensatz nicht jedes mal neu angelegt werden.
- Außerdem wird eine Kursverwaltung benötigt. Die Kurse hier sollen auch auf der Webseite erscheinen. Auf der Webseite melden sich die Teilnehmer für die Kurse an. Die Teilnehmerverwaltung habe ich schon beschrieben. Angemeldete Teilnehmer sollen den Kursen zugeordnet werden. Jeder Kurs hat eine fest maximale Teilnehmeranzahl, wird die überschritten, sollen weitere Anmeldungen auf einer Warteliste zu dem Kurs, Event, Workshop landen. Teilnehmer auf der Warteliste werden über den Status informiert, bei erfolgreiche Anmeldung für einen Kurs, Event oder Workshop natürlich auch.
- Barzahlungen und Überweisungen sollen auch verwaltet werden können. Meldet sich ein Teilnehmer an, soll er angeben, wie er zahlen möchte. Entsprechend der Zahlung soll das im System registriert werden. Für den Teilnehmer soll aus dem System ein Zahluungsbeleg erstellt werden können. Barzahlungen müssen in einer Bareinnahmenliste geführt werden, für Überweisungen gibt es eine Rechnung. In beiden Fällen müssen Nummern geführt werden, die fortlaufend und nicht veränderlich sind.
- Wenn du noch weitere nützliche Funktionen in dem Zusammenhang findest, lass uns darüber sprechen.