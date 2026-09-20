<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Participant\ParticipantEdit;

use Yoga\Modules\Verwaltung\Domain\Participant\Participant;

final readonly class ParticipantEditQuery
{
    /**
     * @return object{id: string, email: string|null, vorname: string, nachname: string, adresszeile_1: string, adresszeile_2: string, postleitzahl: string, stadt: string, telefon: string, geburtsdatum: string, gesundheitsinformationen: string, gesundheitsinformationen_einwilligung: bool, foto_einwilligung: bool, foto_einwilligung_am: string, video_einwilligung: bool, video_einwilligung_am: string, widerruf_am: string, widerrufsvermerk: string, anmeldungen: list<object{id: string, aktivitaet_titel: string, status: string, anmeldedatum: string}>}|null
     */
    public function execute(string $participantId): ?object
    {
        $participant = Participant::findById($participantId);

        if ($participant === null) {
            return null;
        }

        /** @var list<object{id: string, aktivitaet_titel: string, status: string, anmeldedatum: string}> $registrations */
        $registrations = [];
        foreach ($participant->anmeldungen()->with('aktivitaet')->orderBy('angelegt_am', 'desc')->get() as $registration) {
            $activity = $registration->aktivitaet;
            assert($activity !== null);

            $registrations[] = (object) [
                'id' => $registration->id,
                'aktivitaet_titel' => $activity->titel,
                'status' => $registration->status->value,
                'anmeldedatum' => $registration->angelegt_am->format('d.m.Y H:i'),
            ];
        }

        return (object) [
            'id' => $participant->id,
            'email' => $participant->email,
            'vorname' => $participant->vorname,
            'nachname' => $participant->nachname,
            'adresszeile_1' => $participant->adresszeile_1 ?? '',
            'adresszeile_2' => $participant->adresszeile_2 ?? '',
            'postleitzahl' => $participant->postleitzahl ?? '',
            'stadt' => $participant->stadt ?? '',
            'telefon' => $participant->telefon ?? '',
            'geburtsdatum' => $participant->geburtsdatum !== null ? $participant->geburtsdatum->format('Y-m-d') : '',
            'gesundheitsinformationen' => $participant->gesundheitsinformationen ?? '',
            'gesundheitsinformationen_einwilligung' => $participant->gesundheitsinformationen_einwilligung,
            'foto_einwilligung' => $participant->foto_einwilligung,
            'foto_einwilligung_am' => $participant->foto_einwilligung_am !== null ? $participant->foto_einwilligung_am->format('Y-m-d') : '',
            'video_einwilligung' => $participant->video_einwilligung,
            'video_einwilligung_am' => $participant->video_einwilligung_am !== null ? $participant->video_einwilligung_am->format('Y-m-d') : '',
            'widerruf_am' => $participant->widerruf_am !== null ? $participant->widerruf_am->format('Y-m-d') : '',
            'widerrufsvermerk' => $participant->widerrufsvermerk ?? '',
            'anmeldungen' => $registrations,
        ];
    }
}
