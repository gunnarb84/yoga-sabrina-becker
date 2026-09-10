<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\CashReceipt;

/**
 * Schreibt einen Geldbetrag auf Deutsch aus („fünfundvierzig Euro und null Cent“),
 * für das Feld „In Worten“ des Bareinnahmenbelegs.
 */
final readonly class AmountInWords
{
    /** @var list<string> */
    private const EINER = ['', 'ein', 'zwei', 'drei', 'vier', 'fünf', 'sechs', 'sieben', 'acht', 'neun'];

    /** @var list<string> */
    private const ZEHN_BIS_NEUNZEHN = [
        'zehn', 'elf', 'zwölf', 'dreizehn', 'vierzehn', 'fünfzehn',
        'sechzehn', 'siebzehn', 'achtzehn', 'neunzehn',
    ];

    /** @var list<string> */
    private const ZEHNER = ['', '', 'zwanzig', 'dreißig', 'vierzig', 'fünfzig', 'sechzig', 'siebzig', 'achtzig', 'neunzig'];

    public function execute(float $amount): string
    {
        $cents = (int) round($amount * 100);
        $euros = intdiv($cents, 100);
        $centRest = $cents % 100;

        return $this->euros($euros).' Euro und '.$this->euros($centRest).' Cent';
    }

    private function euros(int $amount): string
    {
        if ($amount === 0) {
            return 'null';
        }

        $result = '';
        $millions = intdiv($amount, 1_000_000);
        $rest = $amount % 1_000_000;

        if ($millions > 0) {
            $result .= $millions === 1 ? 'eine Million' : $this->underOneMillion($millions).' Millionen';

            if ($rest > 0) {
                $result .= ' ';
            }
        }

        $result .= $this->underOneMillion($rest);

        if (($rest >= 100 && $rest % 100 === 1) || ($millions > 0 && $rest === 1)) {
            $result .= 's';
        }

        return $result;
    }

    private function underOneMillion(int $amount): string
    {
        if ($amount <= 0) {
            return '';
        }

        if ($amount < 10) {
            return self::EINER[$amount];
        }

        if ($amount < 20) {
            return self::ZEHN_BIS_NEUNZEHN[$amount - 10];
        }

        if ($amount < 100) {
            $ones = $amount % 10;
            $prefix = $ones === 0 ? '' : self::EINER[$ones].'und';

            return $prefix.self::ZEHNER[intdiv($amount, 10)];
        }

        if ($amount < 1000) {
            $hundreds = min(9, max(0, intdiv($amount, 100)));

            return self::EINER[$hundreds].'hundert'.$this->underOneMillion($amount % 100);
        }

        return $this->underOneMillion(intdiv($amount, 1000)).'tausend'.$this->underOneMillion($amount % 1000);
    }
}
