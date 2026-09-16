<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Bareinnahmenliste {{ $monatLabel }}</title>
    <style>
        @page { margin: 14mm 12mm; }

        body {
            margin: 0;
            font-family: Lato, sans-serif;
            color: #0a2621;
            background: #ffffff;
            font-size: 9pt;
        }

        .kopf {
            border-bottom: 1.1pt solid #d9cfb2;
            padding-bottom: 3mm;
            margin-bottom: 4mm;
        }

        .kopf__titel {
            font-size: 16pt;
            font-weight: bold;
            letter-spacing: 1.5pt;
            margin: 0;
        }

        .kopf__untertitel {
            font-size: 10pt;
            color: #5c5952;
            margin: 1mm 0 0 0;
        }

        .kopf__meta {
            font-size: 7.5pt;
            color: #a3874f;
            margin: 1.5mm 0 0 0;
        }

        .kassenbuch { width: 100%; border-spacing: 0; }

        .kassenbuch th {
            font-size: 7.5pt;
            text-transform: uppercase;
            letter-spacing: 0.6pt;
            color: #a3874f;
            border-bottom: 0.9pt solid #d9cfb2;
            text-align: left;
            padding: 1.2mm 1.5mm;
        }

        .kassenbuch td {
            border-bottom: 0.5pt solid #ece5d4;
            padding: 1.2mm 1.5mm;
            vertical-align: top;
        }

        .kassenbuch .rechts { text-align: right; white-space: nowrap; }
        .kassenbuch th.rechts { text-align: right; }

        .uebertrag td {
            border-bottom: 0.9pt solid #d9cfb2;
            font-weight: bold;
            background: #f7f3e8;
        }

        .endbestand td {
            border-top: 0.9pt solid #d9cfb2;
            border-bottom: none;
            font-weight: bold;
            background: #f7f3e8;
        }

        .fuss {
            margin-top: 4mm;
            font-size: 7.5pt;
            color: #5c5952;
        }
    </style>
</head>
<body>
    <div class="kopf">
        <p class="kopf__titel">Bareinnahmenliste</p>
        <p class="kopf__untertitel">{{ $monatLabel }}</p>
        <p class="kopf__meta">Bergen, {{ now()->format('d.m.Y') }}</p>
    </div>

    <table class="kassenbuch">
        <thead>
            <tr>
                <th style="width:17%">Datum</th>
                <th style="width:8%">Art</th>
                <th style="width:17%">Beleg-Nr.</th>
                <th>Empfänger/in bzw. Zweck</th>
                <th class="rechts" style="width:13%">Einnahme</th>
                <th class="rechts" style="width:13%">Ausgabe</th>
                <th class="rechts" style="width:14%">Bestand</th>
            </tr>
        </thead>
        <tbody>
            <tr class="uebertrag">
                <td colspan="4">Übertrag aus den Vormonaten</td>
                <td class="rechts"></td>
                <td class="rechts"></td>
                <td class="rechts">{{ number_format((float) $uebertrag, 2, ',', '.') }} EUR</td>
            </tr>
            @foreach ($rows as $row)
                <tr>
                    <td>{{ $row->datum }}</td>
                    <td>{{ $row->art }}</td>
                    <td>{{ $row->kennung !== '' ? $row->kennung : '—' }}</td>
                    <td>{{ $row->beschreibung }}</td>
                    <td class="rechts">{{ $row->einnahme !== null ? number_format((float) $row->einnahme, 2, ',', '.') : '' }}</td>
                    <td class="rechts">{{ $row->ausgabe !== null ? number_format((float) $row->ausgabe, 2, ',', '.') : '' }}</td>
                    <td class="rechts">{{ number_format((float) $row->bestand, 2, ',', '.') }} {{ $row->waehrung }}</td>
                </tr>
            @endforeach
            <tr class="endbestand">
                <td colspan="4">Endbestand — Übertrag in den Folgemonat</td>
                <td class="rechts"></td>
                <td class="rechts"></td>
                <td class="rechts">{{ number_format((float) $endbestand, 2, ',', '.') }} EUR</td>
            </tr>
        </tbody>
    </table>

    <p class="fuss">
        Beträge in EUR; der Bestand ergibt sich aus dem Übertrag zuzüglich der Bareinnahmen
        abzüglich der Bar-Rückzahlungen und Barentnahmen.
    </p>
</body>
</html>