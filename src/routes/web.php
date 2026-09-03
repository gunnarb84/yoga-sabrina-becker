<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Yoga\Modules\Verwaltung\Ui\Activity\ActivityList;
use Yoga\Modules\Verwaltung\Ui\Activity\CreateActivity;
use Yoga\Modules\Verwaltung\Ui\Activity\EditActivity;
use Yoga\Modules\Verwaltung\Ui\Auth\LoginController;
use Yoga\Modules\Verwaltung\Ui\Dashboard;
use Yoga\Modules\Verwaltung\Ui\Invoice\InvoiceController;
use Yoga\Modules\Verwaltung\Ui\Invoice\Invoices;
use Yoga\Modules\Verwaltung\Ui\OutboundMessage\OutboundMessageDetail;
use Yoga\Modules\Verwaltung\Ui\OutboundMessage\OutboundMessageList;
use Yoga\Modules\Verwaltung\Ui\Participant\CreateParticipant;
use Yoga\Modules\Verwaltung\Ui\Participant\EditParticipant;
use Yoga\Modules\Verwaltung\Ui\Participant\ParticipantList;
use Yoga\Modules\Verwaltung\Ui\Payment\RecordPayment;
use Yoga\Modules\Verwaltung\Ui\Registration\ActivityRegistrations;
use Yoga\Modules\Verwaltung\Ui\Registration\AllRegistrations;
use Yoga\Modules\Verwaltung\Ui\Registration\RegisterParticipant;
use Yoga\Modules\Verwaltung\Ui\Session\CreateSession;
use Yoga\Modules\Webseite\Ui\Activity\ActivityDetail;
use Yoga\Modules\Webseite\Ui\Activity\ActivityOverview;
use Yoga\Modules\Webseite\Ui\Activity\PublishedActivities;
use Yoga\Modules\Webseite\Ui\Page\StaticPage;
use Yoga\Modules\Webseite\Ui\Registration\RegisterForActivity;

Route::get('/', PublishedActivities::class)->name('home');
Route::get('/kurse', ActivityOverview::class)->name('activities');
Route::get('/veranstaltung/{slug}', ActivityDetail::class)->name('activity.detail');
Route::get('/veranstaltung/{slug}/anmelden', RegisterForActivity::class)->name('activity.register');

Route::get('/verwaltung/login', [LoginController::class, 'showLoginForm'])->name('verwaltung.login');
Route::post('/verwaltung/login', [LoginController::class, 'login'])->name('verwaltung.login.submit');

Route::middleware('auth')->group(function (): void {
    Route::get('/verwaltung', Dashboard::class)->name('verwaltung.dashboard');
    Route::get('/verwaltung/dashboard', Dashboard::class)->name('verwaltung.dashboard');
    Route::get('/verwaltung/aktivitaeten', ActivityList::class)->name('verwaltung.activities');
    Route::get('/verwaltung/aktivitaeten/neu', CreateActivity::class)->name('verwaltung.activity.create');
    Route::get('/verwaltung/aktivitaeten/{id}/bearbeiten', EditActivity::class)->name('verwaltung.activity.edit');
    Route::get('/verwaltung/aktivitaeten/{id}/termine/neu', CreateSession::class)->name('verwaltung.session.create');
    Route::get('/verwaltung/aktivitaeten/{id}/anmeldungen', ActivityRegistrations::class)->name('verwaltung.activity.registrations');
    Route::get('/verwaltung/aktivitaeten/{id}/anmeldungen/neu', RegisterParticipant::class)->name('verwaltung.registration.create');
    Route::get('/verwaltung/anmeldungen', AllRegistrations::class)->name('verwaltung.registrations');
    Route::get('/verwaltung/anmeldungen/{id}/zahlung/neu', RecordPayment::class)->name('verwaltung.payment.create');
    Route::get('/verwaltung/rechnungen', Invoices::class)->name('verwaltung.invoices');
    Route::get('/verwaltung/rechnungen/{id}/pdf', [InvoiceController::class, 'download'])->name('verwaltung.rechnung.pdf');
    Route::post('/verwaltung/rechnungen/{id}/email', [InvoiceController::class, 'sendEmail'])->name('verwaltung.rechnung.email');
    Route::get('/verwaltung/nachrichten', OutboundMessageList::class)->name('verwaltung.outbound-messages');
    Route::get('/verwaltung/nachrichten/{id}', OutboundMessageDetail::class)->name('verwaltung.outbound-message.detail');
    Route::get('/verwaltung/teilnehmer', ParticipantList::class)->name('verwaltung.participants');
    Route::get('/verwaltung/teilnehmer/neu', CreateParticipant::class)->name('verwaltung.participant.create');
    Route::get('/verwaltung/teilnehmer/{id}/bearbeiten', EditParticipant::class)->name('verwaltung.participant.edit');
});

Route::post('/verwaltung/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('verwaltung.logout');

Route::get('/login', fn (): \Illuminate\Http\RedirectResponse => redirect()->route('verwaltung.login'))
    ->name('login');

Route::get('/{slug}', StaticPage::class)
    ->where('slug', '[^/]+')
    ->name('page.show');
