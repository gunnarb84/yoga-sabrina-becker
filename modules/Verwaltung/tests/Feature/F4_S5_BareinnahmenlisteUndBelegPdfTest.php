<?php

declare(strict_types=1);

/**
 * Story: E2 CMS & Verwaltung / F4 Zahlungen und Belege verwalten /
 *        S5 Bareinnahmenliste und Beleg-PDF
 *
 * Geprüfte Kriterien:
 * - Die Abfrage ListCashMovements liefert alle Kassenbewegungen gemischt-
 *   chronologisch, neueste zuerst.
 * - Die Bareinnahmenliste zeigt je Zeile den laufenden Bestand: Bareinnahmen als
 *   Zugang, Bar-Rückzahlungen und Barentnahmen als Abgang.
 * - Die Bareinnahmenliste kann nach Beleg- bzw. Fremdbelegnummer und nach
 *   Empfänger/in bzw. Zweck gefiltert werden.
 * - Die Abfrage GenerateCashReceiptPdf erzeugt das PDF eines CashReceipt; sie
 *   scheitert mit dem Fehlercode RECEIPT_NOT_FOUND, wenn der Beleg nicht existiert.
 * - Ein Bareinnahmenbeleg kann als PDF heruntergeladen werden (Dateiname).
 * - Das Beleg-PDF nennt die Belegnummer im Format YYYY-NNNNN sowie
 *   Ausstellungsdatum, Empfänger und Betrag.
 * - Das Beleg-PDF enthält zwei identische Quittungshälften („Original – für
 *   Teilnehmer:in“ oben, „Durchschrift – für Unterlagen“ unten), getrennt durch
 *   eine Trennlinie mit Scherensymbol, jeweils im Aufbau der Papiervorlage.
 * - Jede Quittungshälfte trägt die Felder Beleg-Nr., Datum, Erhalten von, Betrag,
 *   „In Worten“, „Für folgende Leistung / Kurs“, den Kleinunternehmer-Hinweis und
 *   „Betrag dankend bar erhalten.“.
 * - Der Betrag wird „in Worten“ automatisch ausgeschrieben.
 * - Die Zeile „Ort, Datum“ ist mit Bergen und dem Ausstellungsdatum vorausgefüllt.
 */

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Yoga\Modules\Verwaltung\Application\CashReceipt\AmountInWords;
use Yoga\Modules\Verwaltung\Application\CashReceipt\GenerateCashReceiptPdf\GenerateCashReceiptPdf;
use Yoga\Modules\Verwaltung\Application\CashReceipt\GenerateCashReceiptPdf\Request as GeneratePdfRequest;
use Yoga\Modules\Verwaltung\Application\CashReceipt\ListCashMovements\ListCashMovementsQuery;
use Yoga\Modules\Verwaltung\Application\CashWithdrawal\RecordCashWithdrawal\RecordCashWithdrawal;
use Yoga\Modules\Verwaltung\Application\CashWithdrawal\RecordCashWithdrawal\Request as RecordWithdrawalRequest;
use Yoga\Modules\Verwaltung\Application\OutboundMessage\SendOutboundMessage\SendOutboundMessage;
use Yoga\Modules\Verwaltung\Application\Payment\RecordPayment\RecordPayment;
use Yoga\Modules\Verwaltung\Application\Payment\RecordPayment\Request as RecordPaymentRequest;
use Yoga\Modules\Verwaltung\Application\Registration\CancelRegistration\CancelRegistration;
use Yoga\Modules\Verwaltung\Application\Registration\CancelRegistration\Request as CancelRequest;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\RegisterParticipant;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\Request as RegisterRequest;
use Yoga\Modules\Verwaltung\Domain\CashReceipt\CashReceipt;
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
 * Legt eine Anmeldung mit Barzahlung an und liefert Belegnummer und Anmelde-Id.
 */
function erfasseBarzahlung(object $activity, string $email, string $empfaenger, string $datum): object
{
    $participant = TestFactory::createParticipant(email: $email);
    $register = new RegisterParticipant(app(NextNumber::class));
    $registration = $register->execute(new RegisterRequest(
        activityId: $activity->id,
        participantId: $participant->id,
        paymentMethod: 'bar',
    ));

    Carbon::setTestNow($datum);

    $record = new RecordPayment(app(NextNumber::class), new GenerateCashReceiptPdf(new AmountInWords()), new SendOutboundMessage());
    $result = $record->execute(new RecordPaymentRequest(
        registrationId: $registration->unwrap()->registrationId,
        method: 'bar',
        amount: '45.00',
        paidAt: Carbon::now()->format('Y-m-d H:i:s'),
        recipient: $empfaenger,
    ));

    expect($result->isSuccess())->toBeTrue();

    return (object) [
        'nummer' => (string) $result->unwrap()->documentNumber,
        'registrationId' => (string) $registration->unwrap()->registrationId,
    ];
}

/**
 * Erfasst eine Barentnahme und liefert ihre Id.
 */
function erfasseBarentnahme(string $datum, string $betrag, string $zweck): string
{
    $operation = new RecordCashWithdrawal();
    $result = $operation->execute(new RecordWithdrawalRequest(
        date: $datum,
        amount: $betrag,
        purpose: $zweck,
        externalReference: null,
    ));

    expect($result->isSuccess())->toBeTrue();

    return $result->unwrap()->withdrawalId;
}

it('lists cash movements newest first with a running balance', function (): void {
    erfasseBarzahlung($this->activity, 'fruh@example.com', 'Anna Fruh', '2026-09-01 10:00:00');
    erfasseBarzahlung($this->activity, 'spaet@example.com', 'Berta Spat', '2026-09-02 10:00:00');
    erfasseBarentnahme('2026-09-03', '30.00', 'Private Entnahme');

    $query = new ListCashMovementsQuery();
    $movements = $query->execute();

    expect($movements)->toHaveCount(3);

    // Neueste zuerst: Barentnahme, dann Beleg 2, dann Beleg 1.
    expect($movements[0]->typ)->toBe('barentnahme');
    expect($movements[0]->bestand)->toBe('60');
    expect($movements[1]->typ)->toBe('bareinnahme');
    expect($movements[1]->kennung)->toBe('2026-00002');
    expect($movements[1]->bestand)->toBe('90');
    expect($movements[2]->kennung)->toBe('2026-00001');
    expect($movements[2]->bestand)->toBe('45');
});

it('treats cash returns as outgoing movements in the balance', function (): void {
    $zahlung = erfasseBarzahlung($this->activity, 'rueck@example.com', 'Anna Rueck', '2026-09-01 10:00:00');

    $cancel = app(CancelRegistration::class);
    $cancel->execute(new CancelRequest(registrationId: $zahlung->registrationId));

    $query = new ListCashMovementsQuery();
    $movements = $query->execute();

    expect($movements)->toHaveCount(2);

    expect($movements[0]->typ)->toBe('rueckgabe');
    expect($movements[0]->richtung)->toBe('ausgabe');
    expect($movements[0]->bestand)->toBe('0');
    expect($movements[1]->typ)->toBe('bareinnahme');
    expect($movements[1]->richtung)->toBe('einnahme');
    expect($movements[1]->bestand)->toBe('45');
});

it('exposes the required fields for each movement', function (): void {
    erfasseBarzahlung($this->activity, 'felder@example.com', 'Max Feld', '2026-09-02 10:00:00');
    erfasseBarentnahme('2026-09-03', '10.00', 'Wechselgeld');

    $query = new ListCashMovementsQuery();
    $movements = $query->execute();

    expect($movements)->toHaveCount(2);

    $withdrawal = $movements[0];
    expect($withdrawal->typ)->toBe('barentnahme');
    expect($withdrawal->richtung)->toBe('ausgabe');
    expect($withdrawal->datum)->toContain('2026-09-03');
    expect($withdrawal->beschreibung)->toBe('Wechselgeld');
    expect($withdrawal->betrag)->toBe('10.0000');
    expect($withdrawal->waehrung)->toBe('EUR');

    $receipt = $movements[1];
    expect($receipt->typ)->toBe('bareinnahme');
    expect($receipt->richtung)->toBe('einnahme');
    expect($receipt->kennung)->toBe('2026-00001');
    expect($receipt->beschreibung)->toBe('Max Feld');
    expect($receipt->betrag)->toBe('45.0000');
});

it('filters movements by receipt number and external reference', function (): void {
    erfasseBarzahlung($this->activity, 'eins@example.com', 'Max Eins', '2026-09-01 10:00:00');

    $operation = new RecordCashWithdrawal();
    $operation->execute(new RecordWithdrawalRequest(
        date: '2026-09-03',
        amount: '5.00',
        purpose: 'Barkauf Teelichter',
        externalReference: 'KB-9911',
    ));

    $query = new ListCashMovementsQuery();

    $byNumber = $query->execute('00001');
    expect($byNumber)->toHaveCount(1);
    expect($byNumber[0]->typ)->toBe('bareinnahme');

    $byExternalReference = $query->execute('KB-9911');
    expect($byExternalReference)->toHaveCount(1);
    expect($byExternalReference[0]->typ)->toBe('barentnahme');
    expect($byExternalReference[0]->beschreibung)->toBe('Barkauf Teelichter');
});

it('filters movements by recipient and purpose', function (): void {
    erfasseBarzahlung($this->activity, 'anna@example.com', 'Anna Beispiel', '2026-09-01 10:00:00');
    erfasseBarentnahme('2026-09-02', '10.00', 'Wechselgeld ausgezahlt');

    $query = new ListCashMovementsQuery();

    $byRecipient = $query->execute(null, 'Anna Beispiel');
    expect($byRecipient)->toHaveCount(1);
    expect($byRecipient[0]->typ)->toBe('bareinnahme');

    $byPurpose = $query->execute(null, 'Wechselgeld');
    expect($byPurpose)->toHaveCount(1);
    expect($byPurpose[0]->typ)->toBe('barentnahme');
});

it('fails PDF generation with RECEIPT_NOT_FOUND for an unknown receipt', function (): void {
    $generator = new GenerateCashReceiptPdf(new AmountInWords());
    $result = $generator->execute(new GeneratePdfRequest('00000000-0000-0000-0000-000000000000'));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('receipt.not_found');
});

it('generates a receipt PDF with the receipt number as filename', function (): void {
    $zahlung = erfasseBarzahlung($this->activity, 'pdf@example.com', 'Max Pdf', '2026-09-02 10:00:00');
    $nummer = $zahlung->nummer;
    $receiptId = CashReceipt::query()->where('nummer', $nummer)->first()->id;

    $generator = new GenerateCashReceiptPdf(new AmountInWords());
    $result = $generator->execute(new GeneratePdfRequest($receiptId));

    expect($result->isSuccess())->toBeTrue();
    expect($result->unwrap()->filename)->toBe('Barquittung-'.$nummer.'.pdf');
    expect($result->unwrap()->content)->toStartWith('%PDF');
});

it('renders the receipt number, recipient and amount in the receipt document', function (): void {
    $nummer = erfasseBarzahlung($this->activity, 'inhalt@example.com', 'Anna Inhalt', '2026-09-02 10:00:00')->nummer;

    $html = view('bareinnahmenbelege.pdf', [
        'nummer' => $nummer,
        'datum' => '02.09.2026',
        'empfaenger' => 'Anna Inhalt',
        'betrag' => '45,00',
        'inWorten' => (new AmountInWords())->execute(45.00),
        'leistung' => $this->activity->titel,
    ])->render();

    expect($html)->toContain('QUITTUNG');
    expect($html)->toContain('ORIGINAL – FÜR TEILNEHMER:IN');
    expect($html)->toContain('DURCHSCHRIFT – FÜR UNTERLAGEN');
    expect($html)->toContain($nummer);
    expect($html)->toContain('Anna Inhalt');
    expect($html)->toContain('45,00 €');
    expect($html)->toContain('fünfundvierzig Euro und null Cent');
    expect($html)->toContain('02.09.2026');
    expect($html)->toContain('Bergen, 02.09.2026');
    expect($html)->toContain('Kleinunternehmer gemäß § 19 UStG');
});
