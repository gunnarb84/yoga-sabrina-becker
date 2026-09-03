<div class="verwaltung-participants">
    <h1>Teilnehmer</h1>

    <p><a href="{{ route('verwaltung.participant.create') }}">Neuen Teilnehmer anlegen</a></p>

    @if (empty($participants))
        <p>Noch keine Teilnehmer vorhanden.</p>
    @else
        <table class="verwaltung-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>E-Mail</th>
                    <th>Telefon</th>
                    <th>Ort</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($participants as $participant)
                    <tr>
                        <td>
                            <a href="{{ route('verwaltung.participant.edit', ['id' => $participant->id]) }}">
                                {{ $participant->vorname }} {{ $participant->nachname }}
                            </a>
                        </td>
                        <td>{{ $participant->email }}</td>
                        <td>{{ $participant->telefon }}</td>
                        <td>{{ $participant->stadt }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
