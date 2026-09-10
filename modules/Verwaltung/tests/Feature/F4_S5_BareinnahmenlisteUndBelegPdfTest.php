<?php

declare(strict_types=1);

/**
 * Story: E2 CMS & Verwaltung / F4 Zahlungen und Belege verwalten /
 *        S5 Bareinnahmenliste und Beleg-PDF
 *
 * Geprüfte Kriterien:
 * - Die Abfrage ListCashReceipts liefert die Belege, absteigend sortiert nach
 *   issuedAt und number.
 * - Die Bareinnahmenliste kann nach Belegnummer und Empfänger gefiltert werden.
 * - Die Abfrage GenerateCashReceiptPdf erzeugt das PDF eines CashReceipt; sie
 *   scheitert mit dem Fehlercode RECEIPT_NOT_FOUND, wenn der Beleg nicht existiert.
 * - Ein Bareinnahmenbeleg kann als PDF heruntergeladen werden (Dateiname).
 * - Das Beleg-PDF nennt die Belegnummer im Format B-YYYY-NNNNN sowie
 *   Ausstellungsdatum, Empfänger und Betrag.
 */

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Yoga\Modules\Verwaltung\Application\CashReceipt\GenerateCashReceiptPdf\GenerateCashReceiptPdf;
use Yoga\Modules\Verwaltung\Application\CashReceipt\GenerateCashReceiptPdf\Request as GeneratePdfRequest;
use Yoga\Modules\Verwaltung\Application\CashReceipt\ListCashReceipts\ListCashReceiptsQuery;
use Yoga\Modules\Verwaltung\Application\OutboundMessage\SendOutboundMessage\SendOutboundMessage;
use Yoga\Modules\Verwaltung\Application\Payment\RecordPayment\RecordPayment;
use Yoga\Modules\Verwaltung\Application\Payment\RecordPayment\Request as RecordPaymentRequest;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\RegisterParticipant;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\Request as RegisterRequest;
use Yoga\Modules\Verwaltung\Domain\CashReceipt\CashReceipt;
use Yoga\Modules\Verwaltung\Domain\Participant\Participant;
use Yoga\Modules\Verwaltung\Domain\Payment\Payment;
use Yoga\Modules\Verwaltung\Domain\Registration\Registration;
use Yoga\Modules\Verwaltung\Tests\TestFactory;
use Yoga\Platform\NumberSequence\Application\NextNumber;

beforeEach(function (): void {
    Mail::fake();
    Carbon::setTestNow('2026-09-02 12:00:00');
    $this->activity = TestFactory::createActivity(maxParticipants: 2);
});

afterEach(function (): void {
    Carbon::setTestNow();
});

/**
 * Legt eine Anmeldung mit Barzahlung an und liefert die Belegnummer.
 */
function erfasseBarzahlung(object $activity, string $email, string $empfaenger, string $datum): string
{
    $participant = TestFactory::createParticipant(email: $email);
    $register = new RegisterParticipant(app(NextNumber::class));
    $registration = $register->execute(new RegisterRequest(
        activityId: $activity->id,
        participantId: $participant->id,
        paymentMethod: 'bar',
    ));

    Carbon::setTestNow($datum);

    $record = new RecordPayment(app(NextNumber::class), new GenerateCashReceiptPdf(), new SendOutboundMessage());
    $result = $record->execute(new RecordPaymentRequest(
        registrationId: $registration->unwrap()->registrationId,
        method: 'bar',
        amount: '45.00',
        paidAt: Carbon::now()->format('Y-m-d H:i:s'),
        recipient: $empfaenger,
    ));

    expect($result->isSuccess())->toBeTrue();

    return (string) $result->unwrap()->documentNumber;
}

it('lists receipts ordered by issued date and number descending', function (): void {
    erfasseBarzahlung($this->activity, 'fruh@example.com', 'Anna Fruh', '2026-09-01 10:00:00');
    erfasseBarzahlung($this->activity, 'spaet@example.com', 'Berta Spat', '2026-09-02 10:00:00');

    $query = new ListCashReceiptsQuery();
    $receipts = $query->execute();

    expect($receipts)->toHaveCount(2);
    expect($receipts[0]->nummer)->toBe('B-2026-00002');
    expect($receipts[1]->nummer)->toBe('B-2026-00001');
});

it('filters receipts by receipt number', function (): void {
    erfasseBarzahlung($this->activity, 'eins@example.com', 'Max Eins', '2026-09-01 10:00:00');
    erfasseBarzahlung($this->activity, 'zwei@example.com', 'Berta Zwei', '2026-09-02 10:00:00');

    $query = new ListCashReceiptsQuery();
    $receipts = $query->execute('00002');

    expect($receipts)->toHaveCount(1);
    expect($receipts[0]->nummer)->toBe('B-2026-00002');
});

it('filters receipts by recipient', function (): void {
    erfasseBarzahlung($this->activity, 'anna@example.com', 'Anna Beispiel', '2026-09-01 10:00:00');
    erfasseBarzahlung($this->activity, 'berta@example.com', 'Berta Beispiel', '2026-09-02 10:00:00');

    $query = new ListCashReceiptsQuery();
    $receipts = $query->execute(null, 'Anna Beispiel');

    expect($receipts)->toHaveCount(1);
    expect($receipts[0]->empfaenger)->toBe('Anna Beispiel');
});

it('exposes the required fields for each receipt', function (): void {
    erfasseBarzahlung($this->activity, 'felder@example.com', 'Max Feld', '2026-09-02 10:00:00');

    $query = new ListCashReceiptsQuery();
    $receipts = $query->execute();

    expect($receipts)->toHaveCount(1);

    $receipt = $receipts[0];
    expect($receipt->nummer)->toBe('B-2026-00001');
    expect($receipt->ausgestellt_am)->toContain('2026-09-02');
    expect($receipt->empfaenger)->toBe('Max Feld');
    expect($receipt->betrag)->toBe('45.0000');
    expect($receipt->waehrung)->toBe('EUR');
});

it('fails PDF generation with RECEIPT_NOT_FOUND for an unknown receipt', function (): void {
    $generator = new GenerateCashReceiptPdf();
    $result = $generator->execute(new GeneratePdfRequest('00000000-0000-0000-0000-000000000000'));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('receipt.not_found');
});

it('generates a receipt PDF with the receipt number as filename', function (): void {
    $nummer = erfasseBarzahlung($this->activity, 'pdf@example.com', 'Max Pdf', '2026-09-02 10:00:00');
    $receiptId = CashReceipt::query()->where('nummer', $nummer)->first()->id;

    $generator = new GenerateCashReceiptPdf();
    $result = $generator->execute(new GeneratePdfRequest($receiptId));

    expect($result->isSuccess())->toBeTrue();
    expect($result->unwrap()->filename)->toBe('Barquittung-'.$nummer.'.pdf');
    expect($result->unwrap()->content)->toStartWith('%PDF');
});

it('renders the receipt number, recipient and amount in the receipt document', function (): void {
    $nummer = erfasseBarzahlung($this->activity, 'inhalt@example.com', 'Anna Inhalt', '2026-09-02 10:00:00');
    $receipt = CashReceipt::query()->where('nummer', $nummer)->first();
    $payment = Payment::findById($receipt->zahlung_id);
    $registration = Registration::findById($payment->anmeldung_id);
    $participant = Participant::findById($registration->teilnehmer_id);

    $html = view('bareinnahmenbelege.pdf', [
        'receipt' => $receipt,
        'activity' => $this->activity,
        'participant' => $participant,
    ])->render();

    expect($html)->toContain('Barquittung');
    expect($html)->toContain($nummer);
    expect($html)->toContain('Anna Inhalt');
    expect($html)->toContain('45,00');
    expect($html)->toContain('02.09.2026');
});
