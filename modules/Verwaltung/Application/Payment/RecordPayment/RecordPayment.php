<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Payment\RecordPayment;

use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Yoga\Modules\Verwaltung\Domain\CashReceipt\CashReceipt;
use Yoga\Modules\Verwaltung\Domain\Payment\Payment;
use Yoga\Modules\Verwaltung\Domain\Payment\PaymentMethod;
use Yoga\Modules\Verwaltung\Domain\Registration\Registration;
use Yoga\Platform\NumberSequence\Application\NextNumber;
use Yoga\Platform\Shared\Application\Result;

final readonly class RecordPayment
{
    public function __construct(private NextNumber $numbers)
    {
    }

    /** @return Result<Response> */
    public function execute(Request $request): Result
    {
        if ($request->method !== PaymentMethod::Cash->value) {
            return Result::failure('payment.method_not_supported');
        }

        $registration = Registration::findById($request->registrationId);

        if ($registration === null) {
            return Result::failure('registration.not_found');
        }

        $existingPayment = Payment::whereRaw('anmeldung_id = ?', [Uuid::fromString($request->registrationId)->getBytes()])->exists();

        if ($existingPayment) {
            return Result::failure('registration.already_paid');
        }

        return DB::transaction(function () use ($registration, $request): Result {
            $payment = new Payment([
                'anmeldung_id' => $registration->id,
                'methode' => $request->method,
                'betrag' => $request->amount,
                'bezahlt_am' => $request->paidAt ?? now(),
            ]);

            $payment->save();

            [$documentId, $documentNumber, $documentType] = $this->issueCashReceipt($payment, $request->recipient);

            $payment->beleg_id = $documentId;
            $payment->beleg_art = $documentType;
            $payment->save();

            $registration->markAsPaid();
            $registration->save();

            return Result::success(new Response($payment->id, $documentNumber));
        });
    }

    /**
     * @return array{0: string, 1: string, 2: string}
     */
    private function issueCashReceipt(Payment $payment, string $recipient): array
    {
        $number = $this->numbers->next('B');

        $receipt = new CashReceipt([
            'nummer' => $number,
            'zahlung_id' => $payment->id,
            'ausgestellt_am' => now(),
            'empfaenger' => $recipient,
            'betrag' => $payment->betrag,
        ]);

        $receipt->save();

        return [$receipt->id, $number, 'bareinnahmenbeleg'];
    }
}
