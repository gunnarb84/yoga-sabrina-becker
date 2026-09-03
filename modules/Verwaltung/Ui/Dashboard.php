<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Ui;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('verwaltung::layouts.app')]
final class Dashboard extends Component
{
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('verwaltung::dashboard');
    }
}
