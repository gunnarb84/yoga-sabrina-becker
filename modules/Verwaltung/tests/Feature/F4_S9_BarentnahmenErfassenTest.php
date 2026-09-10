<?php

declare(strict_types=1);

/**
 * Story: E2 CMS & Verwaltung / F4 Zahlungen und Belege verwalten /
 *        S9 Barentnahmen erfassen
 *
 * Geprüfte Kriterien:
 * - Der Vorgang RecordCashWithdrawal legt eine CashWithdrawal mit date, amount,
 *   purpose und externalReference an.
 * - Der Vorgang scheitert mit dem Fehlercode PURPOSE_REQUIRED, wenn purpose leer ist.
 * - Der Vorgang scheitert mit dem Fehlercode AMOUNT_INVALID, wenn amount nicht
 *   größer als 0 ist.
 * - Der Vorgang scheitert mit dem Fehlercode DATE_INVALID, wenn date kein gültiges
 *   Datum ist.
 * - Das Feld externalReference ist optional.
 * - Eine Barentnahme erhält keine Belegnummer und kein PDF.
 * - Auf der Bareinnahmen-Seite öffnet der Button „Barentnahme erfassen" die Maske
 *   mit den Feldern date, amount, purpose und externalReference.
 * - Das Feld date ist in der Maske mit dem heutigen Datum vorbelegt.
 * - Nach dem Speichern erscheint die Barentnahme in der Bareinnahmenliste.
 */

use Carbon\Carbon;
use Livewire\Livewire;
use Yoga\Modules\Verwaltung\Application\CashWithdrawal\RecordCashWithdrawal\RecordCashWithdrawal;
use Yoga\Modules\Verwaltung\Application\CashWithdrawal\RecordCashWithdrawal\Request as RecordWithdrawalRequest;
use Yoga\Modules\Verwaltung\Domain\CashReceipt\CashReceipt;
use Yoga\Modules\Verwaltung\Domain\CashWithdrawal\CashWithdrawal;
use Yoga\Modules\Verwaltung\Ui\CashReceipt\CashReceipts;

beforeEach(function (): void {
    Carbon::setTestNow('2026-09-10 12:00:00');
});

afterEach(function (): void {
    Carbon::setTestNow();
});

it('records a cash withdrawal with all fields', function (): void {
    $operation = new RecordCashWithdrawal();

    $result = $operation->execute(new RecordWithdrawalRequest(
        date: '2026-09-10',
        amount: '30,50',
        purpose: 'Barkauf Kursmatten',
        externalReference: 'KB-4471',
    ));

    expect($result->isSuccess())->toBeTrue();

    $withdrawal = CashWithdrawal::query()->firstOrFail();
    expect($withdrawal->datum->format('Y-m-d'))->toBe('2026-09-10');
    expect($withdrawal->betrag)->toBe('30.5000');
    expect($withdrawal->zweck)->toBe('Barkauf Kursmatten');
    expect($withdrawal->fremdbelegnummer)->toBe('KB-4471');
});

it('records a cash withdrawal without an external reference', function (): void {
    $operation = new RecordCashWithdrawal();

    $result = $operation->execute(new RecordWithdrawalRequest(
        date: '2026-09-10',
        amount: '20.00',
        purpose: 'Private Entnahme',
        externalReference: null,
    ));

    expect($result->isSuccess())->toBeTrue();

    $withdrawal = CashWithdrawal::query()->firstOrFail();
    expect($withdrawal->fremdbelegnummer)->toBeNull();
});

it('fails with PURPOSE_REQUIRED when the purpose is empty', function (): void {
    $operation = new RecordCashWithdrawal();

    $result = $operation->execute(new RecordWithdrawalRequest(
        date: '2026-09-10',
        amount: '20.00',
        purpose: '   ',
        externalReference: null,
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('cash_withdrawal.purpose_required');
});

it('fails with AMOUNT_INVALID when the amount is not greater than zero', function (): void {
    $operation = new RecordCashWithdrawal();

    $zero = $operation->execute(new RecordWithdrawalRequest(
        date: '2026-09-10',
        amount: '0',
        purpose: 'Private Entnahme',
        externalReference: null,
    ));

    expect($zero->isFailure())->toBeTrue();
    expect($zero->error()['code'])->toBe('cash_withdrawal.amount_invalid');

    $negative = $operation->execute(new RecordWithdrawalRequest(
        date: '2026-09-10',
        amount: '-5.00',
        purpose: 'Private Entnahme',
        externalReference: null,
    ));

    expect($negative->isFailure())->toBeTrue();
    expect($negative->error()['code'])->toBe('cash_withdrawal.amount_invalid');
});

it('fails with DATE_INVALID when the date is not a valid date', function (): void {
    $operation = new RecordCashWithdrawal();

    $result = $operation->execute(new RecordWithdrawalRequest(
        date: '10.09.2026',
        amount: '20.00',
        purpose: 'Private Entnahme',
        externalReference: null,
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('cash_withdrawal.date_invalid');
});

it('creates no receipt number and no PDF for a cash withdrawal', function (): void {
    $operation = new RecordCashWithdrawal();

    $result = $operation->execute(new RecordWithdrawalRequest(
        date: '2026-09-10',
        amount: '20.00',
        purpose: 'Private Entnahme',
        externalReference: null,
    ));

    expect($result->isSuccess())->toBeTrue();
    expect(CashReceipt::query()->count())->toBe(0);
});

it('opens the withdrawal form with today\'s date pre-filled', function (): void {
    Livewire::test(CashReceipts::class)
        ->assertSee('Barentnahme erfassen')
        ->call('toggleWithdrawalForm')
        ->assertSet('showWithdrawalForm', true)
        ->assertSet('withdrawalDate', '2026-09-10')
        ->assertSee('Zweck')
        ->assertSee('Fremdbelegnummer');
});

it('shows the recorded withdrawal in the cash movement list', function (): void {
    Livewire::test(CashReceipts::class)
        ->call('toggleWithdrawalForm')
        ->set('withdrawalDate', '2026-09-10')
        ->set('withdrawalAmount', '25.00')
        ->set('withdrawalPurpose', 'Wechselgeld ausgezahlt')
        ->set('withdrawalExternalReference', 'KB-100')
        ->call('saveWithdrawal')
        ->assertSet('withdrawalRecorded', true)
        ->assertSee('Wechselgeld ausgezahlt')
        ->assertSee('KB-100')
        ->assertSee('Barentnahme');

    expect(CashWithdrawal::query()->count())->toBe(1);
});

it('shows the error message when the purpose is missing', function (): void {
    Livewire::test(CashReceipts::class)
        ->call('toggleWithdrawalForm')
        ->set('withdrawalDate', '2026-09-10')
        ->set('withdrawalAmount', '25.00')
        ->set('withdrawalPurpose', '')
        ->call('saveWithdrawal')
        ->assertSee('Der Zweck ist ein Pflichtfeld.');

    expect(CashWithdrawal::query()->count())->toBe(0);
});
