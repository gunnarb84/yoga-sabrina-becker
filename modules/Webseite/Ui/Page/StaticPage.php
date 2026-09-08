<?php

declare(strict_types=1);

namespace Yoga\Modules\Webseite\Ui\Page;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Webseite\Application\Page\GetPageContent;

#[Layout('webseite::layouts.app')]
final class StaticPage extends Component
{
    public ?object $page = null;

    public function mount(string $slug, GetPageContent $query): void
    {
        $this->page = $query->execute($slug);
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('webseite::Page.static-page');
    }
}
