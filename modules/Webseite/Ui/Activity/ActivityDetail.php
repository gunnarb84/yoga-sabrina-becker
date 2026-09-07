<?php

declare(strict_types=1);

namespace Yoga\Modules\Webseite\Ui\Activity;

use Illuminate\Support\Facades\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Verwaltung\Application\Activity\PublicDetail\ActivityDetailQuery;

#[Layout('webseite::layouts.app')]
final class ActivityDetail extends Component
{
    public string $slug = '';

    /**
     * @var object{id: string, slug: string, typ: string, typLabel: string, titel: string, kurzbeschreibung: string|null, langbeschreibung: string|null, preis: string, maximale_teilnehmerzahl: int, freie_plaetze: int, warteliste_anzahl: int, ausgebucht: bool, buchbar: bool, bild: string|null, termine: list<object{id: string, beginn: \Carbon\Carbon, ende: \Carbon\Carbon, ort: string|null, hinweis: string|null}>}|null
     */
    public ?object $activity = null;

    public function mount(string $slug, ActivityDetailQuery $query): void
    {
        $this->slug = $slug;

        $result = $query->execute($slug);

        if ($result->isSuccess()) {
            $this->activity = $result->unwrap();
        }
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return View::make('webseite::Activity.activity-detail');
    }
}
