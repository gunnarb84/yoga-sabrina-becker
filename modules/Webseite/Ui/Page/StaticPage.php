<?php

declare(strict_types=1);

namespace Yoga\Modules\Webseite\Ui\Page;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Webseite\Domain\Page\Page;

#[Layout('webseite::layouts.app')]
final class StaticPage extends Component
{
    public ?Page $page = null;

    public function mount(string $slug): void
    {
        $this->page = Page::where('slug', $slug)
            ->where('veroeffentlicht', true)
            ->first();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('webseite::Page.static-page');
    }
}
