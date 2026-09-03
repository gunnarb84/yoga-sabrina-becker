<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Payment\MarkTransferPaid;

use Illuminate\Support\Facades\DB;
use Yoga\Modules\Verwaltung\Domain\Invoice\Invoice;
use Yoga\Modules\Verwaltung\Domain\Invoice\InvoiceStatus;
use Yoga\Modules\Verwaltung\Domain\Payment\Payment;
use Yoga\Modules\Verwaltung\Domain\Registration\Registration;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationPaymentStatus;
use Yoga\Platform\Shared\Application\Result;

final readonly class MarkTransferPaid
{
    public function execute(Request $request): Result
    {
        $invoice = Invoice::findById($request->invoiceId);

        if ($invoice === null) {
            return Result::failure('invoice.not_found');
        }

        $payment = Payment::findById($invoice->getAttribute('zahlung_id'));

        if ($payment === null) {
            return Result::failure('payment.not_found');
        }

        $registration = Registration::findById($payment->getAttribute('anmeldung_id'));

        if ($registration === null) {
            return Result::failure('registration.not_found');
        }

        return DB::transaction(function () use ($invoice, $payment, $registration, $request): Result {
            $newStatus = $invoice->status === InvoiceStatus::Paid
                ? InvoiceStatus::Open
                : InvoiceStatus::Paid;

            $invoice->status = $newStatus;

            if ($request->userId !== null) {
                $invoice->setAttribute('geaendert_von', $request->userId);
            }

            $invoice->save();

            $payment->setAttribute('bezahlt_am', $newStatus === InvoiceStatus::Paid ? now() : null);
            $payment->save();

            $registration->zahlungsstatus = $newStatus === InvoiceStatus::Paid
                ? RegistrationPaymentStatus::Paid
                : RegistrationPaymentStatus::Open;
            $registration->save();

            return Result::success(new Response($payment->getAttribute('id'), $newStatus));
        });
    }
}
