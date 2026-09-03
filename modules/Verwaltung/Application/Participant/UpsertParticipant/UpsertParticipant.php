<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Participant\UpsertParticipant;

use Yoga\Modules\Verwaltung\Domain\Participant\Participant;
use Yoga\Platform\Shared\Application\Result;

final readonly class UpsertParticipant
{
    public function execute(Request $request): Result
    {
        $participant = Participant::where('email', $request->email)->first();
        $wasCreated = false;

        if ($participant === null) {
            $participant = new Participant([
                'email' => $request->email,
            ]);
            $wasCreated = true;
        }

        $participant->vorname = $request->firstName;
        $participant->nachname = $request->lastName;
        $participant->adresszeile_1 = $request->addressLine1;
        $participant->adresszeile_2 = $request->addressLine2;
        $participant->postleitzahl = $request->postalCode;
        $participant->stadt = $request->city;
        $participant->telefon = $request->phone;
        $participant->geburtsdatum = $request->dateOfBirth;

        if ($request->healthNotes !== null && $request->healthNotesConsent) {
            $participant->gesundheitsinformationen = $request->healthNotes;
            $participant->gesundheitsinformationen_einwilligung = true;
        }

        $participant->save();

        return Result::success(new Response($participant->getAttribute('id'), $wasCreated));
    }
}
