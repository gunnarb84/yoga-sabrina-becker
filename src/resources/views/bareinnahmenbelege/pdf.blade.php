<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Barquittung {{ $nummer }}</title>
    <style>
        @page { margin: 0; }

        body {
            margin: 0;
            font-family: Lato, sans-serif;
            color: #0a2621;
            background: #f7f5ed;
        }

        .quittung {
            margin: 9mm 12mm 0 12mm;
            padding: 7mm 9mm 6mm 9mm;
            border: 1.1pt solid #e3dac2;
            border-radius: 5mm;
            background: #fbfaf3;
        }

        .kopf { width: 100%; border-spacing: 0; }
        .kopf__titel {
            font-size: 20pt;
            font-weight: bold;
            letter-spacing: 2pt;
        }
        .kopf__belegnr { text-align: right; vertical-align: bottom; }
        .kopf__nummer {
            font-size: 15pt;
            font-weight: bold;
            margin-top: 1mm;
        }

        .adresse {
            font-size: 7.5pt;
            margin-top: 1.5mm;
        }

        .label {
            font-size: 6.8pt;
            text-transform: uppercase;
            letter-spacing: 0.8pt;
            color: #a3874f;
        }

        .kennzeichnung {
            font-size: 6.8pt;
            text-transform: uppercase;
            letter-spacing: 0.8pt;
            color: #a3874f;
            border-bottom: 0.7pt solid #d9cfb2;
            padding-bottom: 1.5mm;
            margin-top: 3.5mm;
        }

        .felder { width: 100%; border-spacing: 0; margin-top: 3mm; }
        .feld { vertical-align: top; }
        .feld--erste { width: 50%; padding-right: 10mm; }
        .feld--volle { width: 100%; }
        .feld .wert {
            font-size: 9pt;
            border-bottom: 0.7pt solid #b9ad8f;
            padding: 1.2mm 0 0.8mm 0;
            min-height: 4.5mm;
        }
        .wert--betrag { font-size: 11pt; font-weight: bold; }
        .wert--worten { font-size: 8pt; }

        .hinweis {
            font-size: 7.5pt;
            padding: 2.5mm 4mm;
            border-radius: 2mm;
            margin-top: 3.5mm;
        }
        .hinweis--dunkel {
            background: #0a2621;
            color: #e9e4d7;
        }
        .hinweis--hell {
            border: 0.7pt solid #d9cfb2;
            color: #0a2621;
        }

        .bestaetigung {
            font-size: 8.5pt;
            margin-top: 3mm;
        }
        .bestaetigung__kasten {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8pt;
        }
        .bestaetigung__text { margin-left: 1.5mm; }

        .felder--abschluss { margin-top: 5mm; }
        .felder--abschluss .linie {
            border-bottom: 0.7pt solid #b9ad8f;
            height: 6mm;
            font-size: 9pt;
            padding-top: 0.5mm;
        }
        .felder--abschluss .label { margin-top: 1.5mm; }

        .trennung { margin: 4.5mm 12mm 0 12mm; width: 100%; border-spacing: 0; }
        .trennung td { vertical-align: middle; }
        .trennung__linie {
            border-top: 0.5pt dashed #a3874f;
            width: 50%;
        }
        .trennung__schere {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9pt;
            color: #a3874f;
            padding: 0 2mm;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    @foreach ([['kennzeichnung' => 'Original – für Teilnehmer:in', 'dunkel' => true], ['kennzeichnung' => 'Durchschrift – für Unterlagen', 'dunkel' => false]] as $haelfte)
        @include('bareinnahmenbelege.quittung-haelfte', [
            'kennzeichnung' => mb_strtoupper($haelfte['kennzeichnung'], 'UTF-8'),
            'dunkel' => $haelfte['dunkel'],
            'nummer' => $nummer,
            'datum' => $datum,
            'empfaenger' => $empfaenger,
            'betrag' => $betrag,
            'inWorten' => $inWorten,
            'leistung' => $leistung,
        ])
        @if ($loop->first)
            <table class="trennung">
                <tr>
                    <td class="trennung__linie"></td>
                    <td class="trennung__schere">&#x2702;</td>
                    <td class="trennung__linie"></td>
                </tr>
            </table>
        @endif
    @endforeach
</body>
</html>