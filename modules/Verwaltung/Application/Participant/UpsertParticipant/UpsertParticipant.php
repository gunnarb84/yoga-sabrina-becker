<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Participant\UpsertParticipant;

use Carbon\Carbon;
use Yoga\Modules\Verwaltung\Domain\Participant\Participant;
use Yoga\Platform\Shared\Application\Result;

final readonly class UpsertParticipant
{
    /** @return Result<Response> */
    public function execute(Request $request): Result
    {
        // E-Mail normalisieren, damit dieselbe Adresse mit anderer Groß-/Klein-
        // schreibung oder führenden/nachgestellten Leerzeichen denselben
        // Teilnehmer trifft und nicht doppelt angelegt wird.
        $email = mb_strtolower(trim($request->email));

        $participant = Participant::where('email', $email)->first();
        $wasCreated = false;

        if ($participant === null) {
            $participant = new Participant([
                'email' => $email,
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
        $participant->geburtsdatum = $request->dateOfBirth !== null ? Carbon::parse($request->dateOfBirth) : null;

        if ($request->healthNotes !== null && $request->healthNotesConsent) {
            $participant->gesundheitsinformationen = $request->healthNotes;
            $participant->gesundheitsinformationen_einwilligung = true;
        }

        // Für Minderjährige unterschreiben die Sorgeberechtigten den Papierbogen;
        // die Online-Einwilligung wird dann weder gesetzt noch überschrieben.
        if (! $this->isMinor($request->dateOfBirth)) {
            $participant->foto_einwilligung = $request->photoConsent;
            $participant->foto_einwilligung_am = $request->photoConsent ? now() : null;
            $participant->video_einwilligung = $request->videoConsent;
            $participant->video_einwilligung_am = $request->videoConsent ? now() : null;
        }

        $participant->save();

        return Result::success(new Response($participant->id, $wasCreated));
    }

    private function isMinor(?string $dateOfBirth): bool
    {
        return $dateOfBirth !== null && Carbon::parse($dateOfBirth)->age < 18;
    }
}
