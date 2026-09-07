<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Yoga\Modules\Webseite\Domain\Page\Page;

/**
 * Legt die rechtlichen Seiten (Impressum, AGB, Datenschutz) aus den Vorlagen
 * unter vorlagen/ als veröffentlichte CMS-Seiten an.
 */
class LegalPagesSeeder extends Seeder
{
    public function run(): void
    {
        $this->createImpressum();
        $this->createAgb();
        $this->createDatenschutz();
    }

    private function createImpressum(): void
    {
        Page::updateOrCreate(
            ['slug' => 'impressum'],
            [
                'titel' => 'Impressum',
                'inhalt' => <<<'HTML'
<h2>Angaben gemäß § 5 TMG</h2>
<p>
    Yoga Sabrina Becker<br>
    In der Flöz 2<br>
    55608 Bergen
</p>
<p>
    Vertreten durch:<br>
    Sabrina Becker
</p>
<p>
    Kontakt:<br>
    Telefon: 01520 2891446<br>
    E-Mail: <a href="mailto:info@yoga-sabrinabecker.de">info@yoga-sabrinabecker.de</a><br>
    Website: <a href="https://yoga-sabrinabecker.de" rel="noopener noreferrer">https://yoga-sabrinabecker.de</a>
</p>
<h2>Berufsbezeichnung</h2>
<p>Yogalehrerin</p>
<h2>Steuerliche Angaben</h2>
<p>
    Steuernummer: 09/009/32157<br>
    Umsatzsteuer-Identifikationsnummer gemäß § 27a UStG: nicht vorhanden, da Kleinunternehmerin im Sinne von § 19 UStG.
</p>
<h2>Verantwortlich für den Inhalt nach § 55 Abs. 2 RStV</h2>
<p>
    Sabrina Becker<br>
    In der Flöz 2<br>
    55608 Bergen
</p>
<h2>EU-Streitbeilegung</h2>
<p>Die Europäische Kommission stellt eine Plattform zur Online-Streitbeilegung (OS) bereit, die unter folgendem Link erreichbar ist: <a href="https://ec.europa.eu/consumers/odr" target="_blank" rel="noopener noreferrer">https://ec.europa.eu/consumers/odr</a></p>
<p>Wir sind weder verpflichtet noch bereit, an einem Streitbeilegungsverfahren vor einer Verbraucherschlichtungsstelle teilzunehmen.</p>
<h2>Haftung für Inhalte</h2>
<p>Als Diensteanbieter sind wir gemäß § 7 Abs. 1 TMG für eigene Inhalte auf diesen Seiten nach den allgemeinen Gesetzen verantwortlich. Nach §§ 8 bis 10 TMG sind wir als Diensteanbieter jedoch nicht verpflichtet, übermittelte oder gespeicherte fremde Informationen zu überwachen oder nach Umständen zu forschen, die auf eine rechtswidrige Tätigkeit hinweisen.</p>
<p>Verpflichtungen zur Entfernung oder Sperrung der Nutzung von Informationen nach den allgemeinen Gesetzen bleiben hiervon unberührt. Eine diesbezügliche Haftung ist jedoch erst ab dem Zeitpunkt der Kenntnis einer konkreten Rechtsverletzung möglich. Bei Bekanntwerden von entsprechenden Rechtsverletzungen werden wir diese Inhalte umgehend entfernen.</p>
<h2>Haftung für Links</h2>
<p>Unser Angebot enthält Links zu externen Websites Dritter, auf deren Inhalte wir keinen Einfluss haben. Deshalb können wir für diese fremden Inhalte auch keine Gewähr übernehmen. Für die Inhalte der verlinkten Seiten ist stets der jeweilige Anbieter oder Betreiber der Seiten verantwortlich. Die verlinkten Seiten wurden zum Zeitpunkt der Verlinkung auf mögliche Rechtsverstöße überprüft. Rechtswidrige Inhalte waren zum Zeitpunkt der Verlinkung nicht erkennbar.</p>
<p>Eine permanente inhaltliche Kontrolle der verlinkten Seiten ist jedoch ohne konkrete Anhaltspunkte einer Rechtsverletzung nicht zumutbar. Bei Bekanntwerden von Rechtsverletzungen werden wir derartige Links umgehend entfernen.</p>
<h2>Urheberrecht</h2>
<p>Die durch die Seitenbetreiber erstellten Inhalte und Werke auf diesen Seiten unterliegen dem deutschen Urheberrecht. Die Vervielfältigung, Bearbeitung, Verbreitung und jede Art der Verwertung außerhalb der Grenzen des Urheberrechtes bedürfen der schriftlichen Zustimmung des jeweiligen Autors bzw. Erstellers. Downloads und Kopien dieser Seite sind nur für den privaten, nicht kommerziellen Gebrauch gestattet.</p>
<p>Soweit die Inhalte auf dieser Seite nicht vom Betreiber erstellt wurden, werden die Urheberrechte Dritter beachtet. Insbesondere werden Inhalte Dritter als solche gekennzeichnet. Sollten Sie trotzdem auf eine Urheberrechtsverletzung aufmerksam werden, bitten wir um einen entsprechenden Hinweis. Bei Bekanntwerden von Rechtsverletzungen werden wir derartige Inhalte umgehend entfernen.</p>
HTML,
                'meta_beschreibung' => 'Impressum von Yoga Sabrina Becker.',
                'veroeffentlicht' => true,
            ]
        );
    }

    private function createAgb(): void
    {
        Page::updateOrCreate(
            ['slug' => 'agb'],
            [
                'titel' => 'Allgemeine Geschäftsbedingungen (AGB)',
                'inhalt' => <<<'HTML'
<h2>1. Geltungsbereich</h2>
<p>Diese Allgemeinen Geschäftsbedingungen gelten für alle Kurse, Workshops, Einzelstunden und sonstigen Veranstaltungen (nachfolgend „Kurse"), die von <strong>Yoga Sabrina Becker</strong> (nachfolgend „Anbieterin") angeboten und von den Teilnehmer:innen (nachfolgend „Kund:innen") gebucht werden. Sie gelten für Outdoor-Kurse und Präsenzkurse in Studios oder eigenen Räumlichkeiten.</p>
<p>Abweichende oder ergänzende Bedingungen der Kund:innen werden nicht Vertragsbestandteil, es sei denn, die Anbieterin hat diesen ausdrücklich schriftlich zugestimmt.</p>
<h2>2. Vertragsschluss und Buchung</h2>
<p>Ein Vertrag kommt zustande durch:</p>
<ul>
    <li>eine Online-Buchung über die Website oder ein Buchungssystem,</li>
    <li>eine schriftliche Anmeldung per E-Mail oder Messenger,</li>
    <li>eine mündliche Vereinbarung vor Ort oder telefonisch, sofern diese von beiden Seiten bestätigt wurde.</li>
</ul>
<p>Die Anbieterin behält sich vor, Anmeldungen ohne Angabe von Gründen abzulehnen.</p>
<h2>3. Preise und Zahlung</h2>
<p>(1) Die aktuellen Preise sind auf der Website <strong>yoga-sabrinabecker.de</strong> oder in der Kursbeschreibung angegeben. Sie verstehen sich in Euro (€).</p>
<p>(2) Es wird keine Umsatzsteuer ausgewiesen, da die Anbieterin als Kleinunternehmerin im Sinne von § 19 UStG von der Umsatzsteuerpflicht ausgeschlossen ist.</p>
<p>(3) Zahlungen sind vor oder spätestens am Kurstermin fällig, sofern nichts anderes vereinbart wurde. Zahlungsmöglichkeiten sind u. a.:</p>
<ul>
    <li>Banküberweisung auf das Geschäftskonto</li>
    <li>SEPA-Lastschrift (bei erteiltem Mandat)</li>
    <li>Kartenzahlung / Apple Pay / Google Pay über das Buchungssystem</li>
    <li>Bargeld vor Ort (mit Quittung)</li>
</ul>
<p>(4) Bei Zahlungsverzug kann die Anbieterin die Teilnahme am Kurs verweigern.</p>
<p>(5) Die Anbieterin behält sich vor, die Preise für Kurse und Leistungen mit einer Ankündigungsfrist von einem Monat vor dem geplanten Inkrafttreten anzupassen. Bereits gebuchte und bezahlte Kurse werden von Preiserhöhungen nicht berührt.</p>
<h2>4. Mehrfachkarten (z. B. 10er-Karten)</h2>
<p>(1) Die Anbieterin bietet wahlweise Mehrfachkarten für eine bestimmte Anzahl an Kurseinheiten an. Umfang, Geltungsbereich und Preis ergeben sich aus der Kursbeschreibung oder dem individuellen Angebot.</p>
<p>(2) Mehrfachkarten sind nach Erwerb <strong>12 Monate gültig</strong>, sofern nichts anderes vereinbart wurde. Nach Ablauf der Gültigkeit verfallen nicht eingelöste Einheiten. Eine Barauszahlung des Restwerts ist ausgeschlossen.</p>
<p>(3) Die Einlösung erfolgt bei Buchung eines Kurstermins. Erscheint die Kund:in nicht zu einem gebuchten Termin (No-Show) oder storniert sie weniger als 24 Stunden vor Kursbeginn, verfällt die gebuchte Einheit, sofern nicht im Einzelfall etwas anderes vereinbart wurde.</p>
<p>(4) Mehrfachkarten sind grundsätzlich <strong>nicht übertragbar</strong>, es sei denn, die Anbieterin stimmt einer Übertragung ausdrücklich zu.</p>
<p>(5) Bereits bezahlte Mehrfachkarten werden bei einer Preiserhöhung nicht nachträglich erhöht.</p>
<h2>5. Widerrufsrecht bei Online-Buchungen</h2>
<p>(1) Verbraucher haben gemäß § 355 BGB ein <strong>14-tägiges Widerrufsrecht</strong> ab Vertragsschluss, sofern der Kurs nicht vor Ablauf der Widerrufsfrist beginnt.</p>
<p>(2) Beginnt der Kurs innerhalb der Widerrufsfrist und die Kund:in wünscht die frühzeitige Teilnahme, erlischt das Widerrufsrecht, sobald die Anbieterin die Leistung vollständig erbracht hat. Die Kund:in muss vor Beginn ausdrücklich zustimmen, dass das Widerrufsrecht vorzeitig erlischt.</p>
<p>(3) Um das Widerrufsrecht auszuüben, genügt eine eindeutige Erklärung (z. B. per E-Mail an <a href="mailto:info@yoga-sabrinabecker.de">info@yoga-sabrinabecker.de</a>). Zur Wahrung der Frist reicht die rechtzeitige Absendung der Widerrufserklärung.</p>
<h2>6. Stornierung und Rücktritt</h2>
<p>(1) Die Kund:in kann bis <strong>24 Stunden vor Kursbeginn</strong> kostenlos stornieren. Der gezahlte Betrag wird zurückerstattet oder als Guthaben für einen Ersatztermin angerechnet.</p>
<p>(2) Bei Stornierung <strong>weniger als 24 Stunden vor Kursbeginn</strong> oder bei <strong>No-Show</strong> behält die Anbieterin den vollen Kursbetrag ein. Ausnahmen sind im Einzelfall nach billigem Ermessen möglich (z. B. akute Krankheit mit ärztlichem Attest).</p>
<p>(3) Die Anbieterin behält sich vor, einen Kurs abzusagen, wenn die Mindestteilnehmerzahl nicht erreicht wird. Die Mindestteilnehmerzahl beträgt <strong>3 Personen</strong>, sofern in der Kursbeschreibung nichts anderes angegeben ist. Ebenso behält sich die Anbieterin vor, einen Kurs bei Erkrankung der Lehrkraft, Witterung (bei Outdoor) oder höherer Gewalt abzusagen. In diesen Fällen erhalten die Kund:innen eine <strong>volle Erstattung</strong> oder einen kostenlosen Ersatztermin.</p>
<p>(4) Für Mehrfachkarten gelten die Stornierungsregelungen des § 4 ergänzend zu den Regelungen dieses Paragrafen.</p>
<h2>7. Haftung und Haftungsausschluss</h2>
<p>(1) Die Teilnahme am Yoga-Unterricht erfolgt grundsätzlich auf <strong>eigene Verantwortung</strong>. Die Anbieterin übernimmt keine Haftung für Verletzungen oder gesundheitliche Schäden, die während oder nach dem Unterricht auftreten, es sei denn, diese beruhen auf vorsätzlichem oder grob fahrlässigem Handeln der Anbieterin.</p>
<p>(2) Die Kund:in ist verpflichtet, vor der ersten Teilnahme gesundheitliche Risiken offenzulegen und bei Unsicherheit ärztlichen Rat einzuholen. Bei Schwangerschaft, Verletzungen, chronischen Beschwerden oder nach Operationen ist die Kund:in verpflichtet, die Anbieterin vor Kursbeginn zu informieren.</p>
<p>(3) Eine verschuldete Haftung der Anbieterin für leichte Fahrlässigkeit ist auf vertragstypische, vorhersehbare Schäden begrenzt. Dies gilt nicht bei Verletzung von Leben, Körper oder Gesundheit sowie bei Verletzung wesentlicher Vertragspflichten.</p>
<p>(4) Die Haftung für Mitgebrachte Gegenstände (z. B. Yogamatten, Handtücher, Wertgegenstände) ist ausgeschlossen, soweit gesetzlich zulässig.</p>
<p>(5) Die Anbieterin übernimmt keine Verwahrungspflicht für mitgebrachte Gegenstände, Wertgegenstände, Kleidung oder Yogaausrüstung. Die Kund:innen bringen diese Gegenstände auf eigene Verantwortung mit. Die Anbieterin haftet nicht für den Verlust, Diebstahl oder Beschädigung dieser Gegenstände, soweit gesetzlich zulässig.</p>
<h2>8. Gesundheitserklärung</h2>
<p>(1) Mit der Buchung bestätigt die Kund:in:</p>
<ul>
    <li>keine akuten Verletzungen oder frischen Operationen zu haben,</li>
    <li>keine Erkrankungen, die eine gefahrlose Teilnahme am Yoga ausschließen,</li>
    <li>bei Schwangerschaft die Anbieterin vor dem Kurs informiert zu haben,</li>
    <li>keine Beschwerden zu haben, die eine Teilnahme gefährden könnten, ohne ärztlichen Rat eingeholt zu haben.</li>
</ul>
<p>(2) Die Anbieterin kann bei Bedarf ein kurzes Gesundheits- und Teilnahmeformular vor der ersten Teilnahme ausfüllen lassen.</p>
<p>(3) Ein eventuell ausgefülltes Gesundheits- und Teilnahmeformular wird von der Anbieterin nur so lange aufbewahrt, wie es für die Durchführung und Dokumentation der Kursleistung erforderlich ist, längstens jedoch bis zur Beendigung des Vertragsverhältnisses bzw. bis zum Ablauf der gesetzlichen Aufbewahrungsfristen.</p>
<h2>9. Teilnahme Minderjähriger</h2>
<p>(1) Minderjährige können an Kursen teilnehmen, sofern die Kursbeschreibung keine Altersbeschränkung enthält und die gesetzlichen Vertreter der Minderjährigen zugestimmt haben.</p>
<p>(2) Bei der ersten Teilnahme eines Minderjährigen ist von den gesetzlichen Vertretern eine schriftliche Einverständniserklärung abzugeben. Diese kann auch als Teil eines Gesundheits- und Teilnahmeformulars erhoben werden.</p>
<p>(3) Minderjährige Teilnehmer:innen sind verpflichtet, den Anweisungen der Anbieterin Folge zu leisten. Bei Fehlverhalten, das den Kursablauf oder die Sicherheit anderer Teilnehmer:innen beeinträchtigt, kann die Anbieterin die weitere Teilnahme ablehnen.</p>
<p>(4) Für Minderjährige unter 16 Jahren ist sicherzustellen, dass sie pünktlich und sicher am Kursort ankommen bzw. abgeholt werden. Eine Begleitung durch eine erwachsene, sorgeberechtigte Person ist erforderlich, sofern nicht ausdrücklich eine selbstständige An- und Abreise vereinbart wurde.</p>
<p>(5) Die gesetzlichen Vertreter haften für Schäden, die von Minderjährigen während der Kursteilnahme verursacht werden, soweit sie ihrer Aufsichtspflicht nicht nachgekommen sind.</p>
<h2>10. Verhalten während der Kurse</h2>
<p>(1) Die Kund:in folgt den Anweisungen der Anbieterin und achtet auf die eigenen körperlichen Grenzen.</p>
<p>(2) Bei Unwohlsein, Schmerzen oder Schwindel informiert die Kund:in die Anbieterin unverzüglich und pausiert die Übung.</p>
<p>(3) Die Kund:in stellt sicher, dass der Übungsplatz (Outdoor oder Studio) sicher und frei von Stolperfallen ist.</p>
<h2>11. Outdoor-Kurse</h2>
<p>(1) Outdoor-Kurse sind wetterabhängig. Bei Gewitter, Starkregen, Sturm oder extremen Temperaturen kann die Anbieterin den Termin kurzfristig absagen. Die Kund:innen werden rechtzeitig informiert und erhalten Erstattung oder einen Ersatztermin.</p>
<p>(2) Teilnehmer:innen sind für wetterangemessene Kleidung, ausreichend Trinken, Sonnenschutz (bei Bedarf) und geeignete Unterlagen selbst verantwortlich.</p>
<p>(3) Es liegt in der Verantwortung der Kund:in, den angegebenen Treffpunkt rechtzeitig zu finden. Bei Verspätung besteht kein Anspruch auf eine verlängerte Kurszeit.</p>
<h2>12. Foto- und Videoaufnahmen</h2>
<p>(1) Die Anbieterin dokumentiert Kurse, Workshops und Veranstaltungen gelegentlich fotografisch oder filmisch, um diese für Werbezwecke auf der Website, in Social Media oder in Printmedien zu verwenden.</p>
<p>(2) Mit der Buchung erklären die Kund:innen ihr Einverständnis damit, dass sie im Rahmen von Gruppenaufnahmen abgebildet werden können, sofern sie nicht ausdrücklich widersprechen. Ein Widerspruch kann jederzeit schriftlich oder per E-Mail an <a href="mailto:info@yoga-sabrinabecker.de">info@yoga-sabrinabecker.de</a> erklärt werden.</p>
<p>(3) Bei besonders eindeutigen oder nah aufgenommenen Porträtaufnahmen erfolgt eine gesonderte Einwilligung der abgebildeten Person vor der Veröffentlichung.</p>
<p>(4) Die Kund:innen sind nicht berechtigt, Kurse, andere Teilnehmer:innen oder die Anbieterin ohne deren ausdrückliche Zustimmung fotografisch oder filmisch aufzunehmen und zu veröffentlichen.</p>
<p>(5) Auf Wunsch werden abgebildete Personen bei bereits veröffentlichten Aufnahmen unkenntlich gemacht oder die Aufnahme entfernt, soweit technisch zumutbar.</p>
<h2>13. Hausrecht und Ausschluss</h2>
<p>(1) Die Anbieterin ist berechtigt, Kund:innen, die den Kursablauf stören, andere Teilnehmer:innen belästigen oder gegen die Anweisungen der Anbieterin verstoßen, nach einer Abmahnung von der weiteren Teilnahme auszuschließen.</p>
<p>(2) Im Falle eines Ausschlusses besteht kein Anspruch auf Rückerstattung des Kursentgelts, es sei denn, die Anbieterin entscheidet im Einzelfall nach billigem Ermessen anders.</p>
<p>(3) Ein sofortiger Ausschluss ohne vorherige Abmahnung ist zulässig, wenn ein angemessenes Interesse der übrigen Teilnehmer:innen oder der Anbieterin dies erfordert, insbesondere bei:</p>
<ul>
    <li>aggressiven, rassistischen, sexistischen oder diskriminierenden Äußerungen,</li>
    <li>körperlicher Gewalt oder deren Androhung,</li>
    <li>sexueller Belästigung,</li>
    <li>grober Missachtung von Sicherheitshinweisen,</li>
    <li>Erscheinen in einem offensichtlich alkohol- oder drogenbeeinflussten Zustand.</li>
</ul>
<p>(4) Die Anbieterin ist berechtigt, betrunkene, aggressive oder anderweitig auffällige Personen den Zutritt zum Kurs zu verwehren.</p>
<p>(5) Ein von der Anbieterin ausgesprochenes Hausverbot gilt zeitlich befristet oder unbefristet, je nach Schwere des Verhaltens. Die Anbieterin informiert die betroffene Person über Art und Dauer des Ausschlusses.</p>
<h2>14. Verspätung</h2>
<p>(1) Die Kund:innen sind verpflichtet, pünktlich zum Kursbeginn am angegebenen Treffpunkt bzw. Kursort zu erscheinen.</p>
<p>(2) Bei verspätetem Erscheinen besteht kein Anspruch auf eine verlängerte Kurszeit oder auf eine Wiederholung von bereits durchgeführten Übungen.</p>
<p>(3) Sollte die Anbieterin aus organisatorischen Gründen verspätet beginnen, wird die versäumte Zeit nach Möglichkeit nachgeholt oder der Kurs entsprechend verlängert, sofern dies zeitlich vertretbar ist.</p>
<p>(4) Bei Verspätung von mehr als 15 Minuten kann die Anbieterin die Teilnahme verweigern, wenn eine verspätete Teilnahme den Kursablauf oder die Sicherheit anderer Teilnehmer:innen beeinträchtigen würde.</p>
<h2>15. Warteliste</h2>
<p>(1) Ist ein Kurs ausgebucht, können sich Interessent:innen auf eine Warteliste setzen lassen. Die Aufnahme in die Warteliste ist unverbindlich und begründet noch keinen Vertragsanspruch.</p>
<p>(2) Werden freie Plätze verfügbar (z. B. durch Stornierung), werden die Personen auf der Warteliste in der Reihenfolge ihrer Eintragung kontaktiert. Die Kund:innen erhalten eine Frist von <strong>24 Stunden</strong>, innerhalb derer sie den verfügbaren Platz verbindlich buchen müssen. Nach Ablauf der Frist wird der Platz an die nächste Person auf der Warteliste vergeben.</p>
<p>(3) Die Anbieterin behält sich vor, Wartelisten jederzeit aufzulösen, wenn sie nicht mehr erforderlich sind.</p>
<h2>16. Datenschutz</h2>
<p>Die Anbieterin verarbeitet personenbezogene Daten nur im für die Durchführung des Kurses erforderlichen Umfang. Weitere Informationen sind in der Datenschutzerklärung auf der Website <strong>yoga-sabrinabecker.de</strong> zu finden.</p>
<h2>17. Schlussbestimmungen</h2>
<p>(1) Es gilt das Recht der Bundesrepublik Deutschland unter Ausschluss des UN-Kaufrechts.</p>
<p>(2) Gerichtsstand ist, soweit gesetzlich zulässig, der Sitz der Anbieterin <strong>(55608 Bergen)</strong>.</p>
<p>(3) Sollten einzelne Bestimmungen dieser AGB unwirksam sein oder werden, bleibt der Rest der AGB wirksam (Salvatorische Klausel).</p>
<h2>18. Änderung der AGB</h2>
<p>(1) Die Anbieterin kann diese Allgemeinen Geschäftsbedingungen ändern, soweit dies aus rechtlichen oder tatsächlichen Gründen erforderlich ist oder wenn die Änderungen für die Kund:innen zumutbar sind.</p>
<p>(2) Über Änderungen werden die Kund:innen rechtzeitig, spätestens jedoch einen Monat vor dem geplanten Inkrafttreten, in Textform informiert. Sofern die Kund:innen nicht innerhalb von zwei Wochen nach Zugang der Änderungsmitteilung widersprechen, gelten die Änderungen als akzeptiert.</p>
<p>(3) Im Falle eines Widerspruchs endet das Vertragsverhältnis zum Zeitpunkt des Inkrafttretens der geplanten Änderung, soweit ein laufendes Vertragsverhältnis besteht.</p>
<h2>19. Workshops, Retreats und Sonderveranstaltungen</h2>
<p>(1) Für Workshops, Retreats und sonstige Sonderveranstaltungen gelten ergänzend zu diesen AGB die in der jeweiligen Veranstaltungsbeschreibung genannten besonderen Bedingungen, insbesondere zu Preis, Umfang, Termin, Ort, Verpflegung, Übernachtung und An-/Abreise.</p>
<p>(2) Die Stornobedingungen nach § 6 gelten grundsätzlich auch für Workshops und Retreats, sofern in der Veranstaltungsbeschreibung keine abweichenden Regelungen getroffen wurden. Bei Retreats mit Übernachtung oder externen Kosten können abweichende Stornofristen und -gebühren gelten, die vor Buchung mitgeteilt werden.</p>
<p>(3) Die Anbieterin behält sich vor, den Inhalt und den Ablauf von Workshops und Retreats an die Gegebenheiten vor Ort, die Witterung oder die Teilnehmergruppe anzupassen, soweit dies zumutbar ist und die wesentliche Vertragsleistung nicht beeinträchtigt wird.</p>
<p>(4) Teilnehmer:innen sind für ihre An- und Abreise sowie für die Einhaltung etwaiger Reisebestimmungen selbst verantwortlich.</p>
<h2>20. Vertragsübertragung</h2>
<p>(1) Die Buchung einer Kurseinheit ist grundsätzlich <strong>nicht übertragbar</strong>, sofern nicht ausdrücklich etwas anderes vereinbart wurde.</p>
<p>(2) In begründeten Einzelfällen (z. B. akute Krankheit) kann die Anbieterin nach vorheriger Anfrage und Mitteilung der Daten der Ersatzperson zustimmen, dass ein bereits gebuchter Kurstermin von einer anderen Person wahrgenommen wird. Diese Zustimmung kann schriftlich oder per E-Mail an <a href="mailto:info@yoga-sabrinabecker.de">info@yoga-sabrinabecker.de</a> erteilt werden.</p>
<p>(3) Bei kostenlosen oder vergünstigten Angeboten (z. B. Schnupperstunden, spezielle Aktionen) ist eine Übertragung ausgeschlossen.</p>
HTML,
                'meta_beschreibung' => 'Allgemeine Geschäftsbedingungen für Kurse, Workshops und Veranstaltungen von Yoga Sabrina Becker.',
                'veroeffentlicht' => true,
            ]
        );
    }

    private function createDatenschutz(): void
    {
        Page::updateOrCreate(
            ['slug' => 'datenschutz'],
            [
                'titel' => 'Datenschutzerklärung',
                'inhalt' => <<<'HTML'
<p>Stand: 18.08.2026</p>
<h2>1. Verantwortliche</h2>
<p>Verantwortlich für die Datenverarbeitung auf dieser Website ist:</p>
<p>
    Yoga Sabrina Becker<br>
    In der Flöz 2<br>
    55608 Bergen
</p>
<p>
    Telefon: 01520 2891446<br>
    E-Mail: <a href="mailto:info@yoga-sabrinabecker.de">info@yoga-sabrinabecker.de</a><br>
    Website: <a href="https://yoga-sabrinabecker.de" rel="noopener noreferrer">https://yoga-sabrinabecker.de</a>
</p>
<h2>2. Allgemeines zur Datenverarbeitung</h2>
<p>Wir nehmen den Schutz Ihrer persönlichen Daten sehr ernst. Personenbezogene Daten werden auf dieser Website nur im technisch notwendigen und vertraglich erforderlichen Umfang erhoben, verarbeitet und genutzt.</p>
<p>Eine Nutzung unserer Website ist grundsätzlich ohne Angabe personenbezogener Daten möglich. Soweit personenbezogene Daten (z. B. Name, Anschrift, E-Mail-Adresse, Telefonnummer) erhoben werden, erfolgt dies – soweit möglich – freiwillig.</p>
<h2>3. Hosting</h2>
<p>Diese Website wird bei der <strong>IONOS SE</strong> gehostet:</p>
<p>
    IONOS SE<br>
    Elgendorfer Str. 57<br>
    56410 Montabaur<br>
    Deutschland
</p>
<p>Beim Aufrufen unserer Website werden aus technischer Notwendigkeit Verbindungsdaten (z. B. IP-Adresse, Datum, Uhrzeit, aufgerufene Seite, Browser- und Betriebssysteminformationen) an IONOS übertragen und in Server-Logfiles gespeichert. Diese Daten werden ausschließlich zur technischen Bereitstellung und Sicherstellung des Betriebs der Website verwendet.</p>
<p><strong>Rechtsgrundlage:</strong> Art. 6 Abs. 1 lit. f DSGVO (berechtigtes Interesse an technisch fehlerfreier und sicherer Bereitstellung der Website).</p>
<h2>4. Kontaktaufnahme per E-Mail oder Telefon</h2>
<p>Wenn Sie uns per E-Mail oder Telefon kontaktieren, werden die von Ihnen übermittelten Daten (z. B. Name, E-Mail-Adresse, Telefonnummer, Anliegen) zum Zweck der Bearbeitung Ihrer Anfrage gespeichert.</p>
<p><strong>Rechtsgrundlage:</strong> Art. 6 Abs. 1 lit. b DSGVO (vorvertragliche Maßnahmen bzw. Vertragserfüllung) und Art. 6 Abs. 1 lit. f DSGVO (berechtigtes Interesse an Beantwortung der Anfrage).</p>
<h2>5. Online-Buchungssystem</h2>
<p>Auf unserer Website ist ein Online-Buchungssystem geplant, über das Kurse, Workshops und Einzelstunden gebucht werden können. Bei Nutzung dieses Buchungssystems werden personenbezogene Daten (z. B. Name, E-Mail-Adresse, Telefonnummer, Buchungsdaten) erfasst, verarbeitet und gespeichert.</p>
<p>Sobald das Buchungssystem feststeht, wird diese Datenschutzerklärung um die Details zum jeweiligen Anbieter ergänzt.</p>
<p><strong>Rechtsgrundlage:</strong> Art. 6 Abs. 1 lit. b DSGVO (Vertragserfüllung).</p>
<h2>6. Instagram-Feed</h2>
<p>Auf unserer Website kann ein Feed von Instagram eingebunden sein. Beim Aufruf der Website kann es daher zu einer Datenübertragung an die Meta Platforms Ireland Ltd., 4 Grand Canal Square, Grand Canal Harbour, Dublin 2, Irland, kommen. Meta verarbeitet die Daten möglicherweise in den USA.</p>
<p>Bitte beachten Sie die Datenschutzhinweise von Instagram: <a href="https://help.instagram.com/519522125107875" target="_blank" rel="noopener noreferrer">https://help.instagram.com/519522125107875</a></p>
<p><strong>Rechtsgrundlage:</strong> Art. 6 Abs. 1 lit. f DSGVO (berechtigtes Interesse an einer ansprechenden Darstellung unserer Social-Media-Inhalte).</p>
<h2>7. Cookies</h2>
<p>Diese Website verwendet keine eigenen Analyse-, Tracking- oder Marketing-Cookies. Sofern das Online-Buchungssystem oder der Instagram-Feed Cookies setzen, erfolgt dies durch die jeweiligen Drittanbieter. Weitere Informationen entnehmen Sie bitte den Datenschutzhinweisen der jeweiligen Anbieter.</p>
<h2>8. Ihre Rechte</h2>
<p>Sie haben das Recht:</p>
<ul>
    <li>gemäß Art. 15 DSGVO Auskunft über Ihre von uns verarbeiteten personenbezogenen Daten zu verlangen,</li>
    <li>gemäß Art. 16 DSGVO unverzüglich die Berichtigung unrichtiger oder Vervollständigung Ihrer bei uns gespeicherten personenbezogenen Daten zu verlangen,</li>
    <li>gemäß Art. 17 DSGVO die Löschung Ihrer bei uns gespeicherten personenbezogenen Daten zu verlangen,</li>
    <li>gemäß Art. 18 DSGVO die Einschränkung der Verarbeitung Ihrer personenbezogenen Daten zu verlangen,</li>
    <li>gemäß Art. 20 DSGVO Ihre personenbezogenen Daten in einem strukturierten, gängigen und maschinenlesbaren Format zu erhalten,</li>
    <li>gemäß Art. 21 DSGVO Widerspruch gegen die Verarbeitung einzulegen,</li>
    <li>gemäß Art. 77 DSGVO sich bei einer Aufsichtsbehörde zu beschweren.</li>
</ul>
<h2>9. Änderungen der Datenschutzerklärung</h2>
<p>Wir behalten uns vor, diese Datenschutzerklärung bei Änderungen der rechtlichen Grundlagen oder der eingesetzten Dienste anzupassen. Die aktuelle Fassung ist auf unserer Website unter <a href="https://yoga-sabrinabecker.de/datenschutz" rel="noopener noreferrer">https://yoga-sabrinabecker.de/datenschutz</a> abrufbar.</p>
HTML,
                'meta_beschreibung' => 'Datenschutzerklärung der Webseite von Yoga Sabrina Becker.',
                'veroeffentlicht' => true,
            ]
        );
    }
}
