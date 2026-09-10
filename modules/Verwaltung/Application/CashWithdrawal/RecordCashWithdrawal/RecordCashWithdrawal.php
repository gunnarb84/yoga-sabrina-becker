<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\CashWithdrawal\RecordCashWithdrawal;

use Carbon\Carbon;
use Yoga\Modules\Verwaltung\Domain\CashWithdrawal\CashWithdrawal;
use Yoga\Platform\Shared\Application\Result;

/**
 * Erfasst eine Barentnahme aus der Barkasse. Eine Barentnahme ist kein
 * Belegvorgang: Sie erhält keine Belegnummer und kein PDF.
 */
final readonly class RecordCashWithdrawal
{
    /** @return Result<Response> */
    public function execute(Request $request): Result
    {
        $amount = (float) str_replace(',', '.', $request->amount);

        if ($amount <= 0.0) {
            return Result::failure('cash_withdrawal.amount_invalid');
        }

        $purpose = trim($request->purpose);

        if ($purpose === '') {
            return Result::failure('cash_withdrawal.purpose_required');
        }

        if (! Carbon::hasFormat($request->date, 'Y-m-d')) {
            return Result::failure('cash_withdrawal.date_invalid');
        }

        $externalReference = trim((string) $request->externalReference);

        $withdrawal = new CashWithdrawal([
            'datum' => $request->date,
            'betrag' => $amount,
            'zweck' => $purpose,
            'fremdbelegnummer' => $externalReference !== '' ? $externalReference : null,
        ]);

        $withdrawal->save();

        return Result::success(new Response(
            $withdrawal->id,
            $amount,
            $purpose,
        ));
    }
}
