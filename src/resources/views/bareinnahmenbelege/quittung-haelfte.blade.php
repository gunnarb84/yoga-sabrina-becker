<div class="quittung">
    <table class="kopf">
        <tr>
            <td class="kopf__titel">QUITTUNG</td>
            <td class="kopf__belegnr">
                <div class="label">Beleg-Nr.</div>
                <div class="kopf__nummer">{{ $nummer }}</div>
            </td>
        </tr>
    </table>

    <div class="adresse">Yoga Sabrina Becker · In der Flöz 2, 55608 Bergen · Steuernummer 09/009/32157</div>

    <div class="kennzeichnung">{{ $kennzeichnung }}</div>

    <table class="felder">
        <tr>
            <td class="feld feld--erste">
                <div class="label">Datum</div>
                <div class="wert">{{ $datum }}</div>
            </td>
            <td class="feld">
                <div class="label">Erhalten von</div>
                <div class="wert">{{ $empfaenger }}</div>
            </td>
        </tr>
        <tr>
            <td class="feld feld--erste">
                <div class="label">Betrag</div>
                <div class="wert wert--betrag">{{ $betrag }} €</div>
            </td>
            <td class="feld">
                <div class="label">In Worten</div>
                <div class="wert wert--worten">{{ $inWorten }}</div>
            </td>
        </tr>
        <tr>
            <td class="feld feld--volle" colspan="2">
                <div class="label">Für folgende Leistung / Kurs</div>
                <div class="wert">{{ $leistung }}</div>
            </td>
        </tr>
    </table>

    <div class="hinweis {{ $dunkel ? 'hinweis--dunkel' : 'hinweis--hell' }}">
        Kleinunternehmer gemäß § 19 UStG – kein gesonderter Umsatzsteuerausweis.
    </div>

    <div class="bestaetigung">
        <span class="bestaetigung__kasten">☑</span>
        <span class="bestaetigung__text">Betrag dankend bar erhalten.</span>
    </div>

    <table class="felder felder--abschluss">
        <tr>
            <td class="feld feld--erste">
                <div class="linie"></div>
                <div class="label">Ort, Datum</div>
            </td>
            <td class="feld">
                <div class="linie"></div>
                <div class="label">Unterschrift (Kursleitung)</div>
            </td>
        </tr>
    </table>
</div>