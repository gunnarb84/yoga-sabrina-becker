<div class="yoga-section yoga-section--creme yoga-section--flush">
<div class="yoga-static-page">
    @if ($page !== null)
        @section('title', $page->titel . ' - Yoga mit Sabrina Becker')
        @if ($page->meta_beschreibung)
            @section('meta-description', $page->meta_beschreibung)
        @endif
        <h1>{{ $page->titel }}</h1>
    @endif

    @if ($submitted)
        <div class="yoga-success-box">
            <h2>Vielen Dank für Ihre Anfrage!</h2>
            <p class="yoga-lead">Ihre Anfrage ist angekommen. Sie erhalten eine Eingangsbestätigung per E-Mail, und ich melde mich persönlich bei Ihnen zurück.</p>
        </div>
    @else
        <div class="yoga-contact-grid">
            @if ($page !== null)
                <div class="yoga-page-content">{!! $page->inhalt !!}</div>
            @endif

            <div class="yoga-form-card">
                <p class="yoga-card-label">Anfrage senden</p>
                <h3>Schreib mir</h3>

                @if ($error !== '')
                    <p class="error">{{ $error }}</p>
                @endif

                <form wire:submit="submit" class="yoga-form">
                    <div class="yoga-form-group">
                        <label for="name">Name *</label>
                        <input type="text" id="name" wire:model="name" required>
                    </div>

                    <div class="yoga-form-group">
                        <label for="email">E-Mail *</label>
                        <input type="email" id="email" wire:model="email" required>
                    </div>

                    <div class="yoga-form-group">
                        <label for="phone">Telefon</label>
                        <input type="tel" id="phone" wire:model="phone">
                    </div>

                    <div class="yoga-form-group">
                        <label for="topic">Anlass / Gruppe</label>
                        <input type="text" id="topic" wire:model="topic">
                    </div>

                    <div class="yoga-form-group">
                        <label for="message">Nachricht *</label>
                        <textarea id="message" wire:model="message" rows="5" required></textarea>
                    </div>

                    <div class="yoga-form-group" aria-hidden="true" style="display:none">
                        <label for="website">Website</label>
                        <input type="text" id="website" wire:model="website" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="yoga-form-group yoga-mt-2">
                        <button type="submit">Anfrage senden</button>
                        <p class="yoga-form-hint">
                            Mit dem Absenden bestätigen Sie die Speicherung Ihrer Daten zum Zweck der Bearbeitung.
                            Details: <a href="/datenschutz">Datenschutzerklärung</a>.
                        </p>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
</div>