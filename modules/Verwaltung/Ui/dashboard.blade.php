<div class="au-panel">
    <div class="au-panel__header">
        <h1 class="au-panel__title">Dashboard</h1>
    </div>

    <div class="au-panel__body">
        <div class="yoga-form-grid yoga-mb-3">
            <div class="au-panel yoga-mb-0">
                <div class="au-panel__body">
                    <h2 class="yoga-subtitle yoga-mb-2">Nächste Termine</h2>

                    @if (count($sessions) === 0)
                        <p class="yoga-empty">Keine Termine im laufenden Monat.</p>
                    @else
                        <div class="yoga-stack">
                            @foreach ($sessions as $session)
                                <a href="{{ route('verwaltung.activity.registrations', ['id' => $session->aktivitaetId]) }}"
                                   class="au-panel yoga-mb-0"
                                   style="display:flex; align-items:center; justify-content:space-between; gap:1rem; padding:0.75rem 1rem; text-decoration:none;">
                                    <span>
                                        <strong>{{ $session->titel }}</strong><br>
                                        <span class="yoga-status-not-bookable">{{ \Carbon\Carbon::parse($session->beginn)->format('d.m.Y, H:i') }}</span>
                                    </span>
                                    <span style="text-align:right; white-space:nowrap;">
                                        {{ $session->confirmed }} bestätigt<br>
                                        <span class="yoga-status-not-bookable">{{ $session->freeSeats }} freie Plätze</span>
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="au-panel yoga-mb-0">
                <div class="au-panel__body">
                    <h2 class="yoga-subtitle yoga-mb-2">Offene Aufgaben</h2>

                    @foreach ($tasks as $task)
                        @if ($task->count === null || $task->count > 0)
                            <a href="{{ $task->href }}"
                               style="display:flex; align-items:center; justify-content:space-between; gap:1rem; padding:0.5rem 0; text-decoration:none;">
                                <span>{{ $task->label }}</span>
                                @if ($task->count !== null)
                                    <span class="au-status au-status--warn">{{ $task->count }}</span>
                                @endif
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <div class="yoga-form-grid yoga-mb-3">
            <div class="au-panel yoga-mb-0">
                <div class="au-panel__body">
                    <h2 class="yoga-subtitle yoga-mb-2">Kennzahlen</h2>

                    <div class="yoga-form-grid">
                        @foreach ($metrics as $metric)
                            <div>
                                <span class="yoga-status-not-bookable">{{ $metric->label }}</span><br>
                                <strong>{{ $metric->value }}</strong>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="au-panel yoga-mb-0">
                <div class="au-panel__body">
                    <h2 class="yoga-subtitle yoga-mb-2">Schnellzugriffe</h2>

                    <div class="yoga-form-actions">
                        @foreach ($shortcuts as $shortcut)
                            <a href="{{ $shortcut->href }}" class="au-btn">{{ $shortcut->label }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>