<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Participant\CreateParticipant;

use Yoga\Modules\Verwaltung\Domain\Participant\Participant;
use Yoga\Platform\Shared\Application\Result;

final readonly class CreateParticipant
{
    public function execute(Request $request): Result
    {
        if (Participant::where('email', $request->email)->exists()) {
            return Result::failure('participant.email_already_exists');
        }

        $participant = new Participant([
            'email' => $request->email,
            'vorname' => $request->firstName,
            'nachname' => $request->lastName,
            'adresszeile_1' => $request->addressLine1,
            'adresszeile_2' => $request->addressLine2,
            'postleitzahl' => $request->postalCode,
            'stadt' => $request->city,
            'telefon' => $request->phone,
            'geburtsdatum' => $request->dateOfBirth,
            'gesundheitsinformationen' => $request->healthNotes,
            'gesundheitsinformationen_einwilligung' => $request->healthNotes !== null,
        ]);

        $participant->save();

        return Result::success(new Response($participant->getAttribute('id')));
    }
}
