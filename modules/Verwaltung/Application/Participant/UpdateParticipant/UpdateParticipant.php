<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Participant\UpdateParticipant;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Yoga\Modules\Verwaltung\Domain\Participant\Participant;
use Yoga\Platform\Shared\Application\Result;

final readonly class UpdateParticipant
{
    /** @return Result<Response> */
    public function execute(Request $request): Result
    {
        $participant = Participant::findById($request->participantId);

        if ($participant === null) {
            return Result::failure('participant.not_found');
        }

        if ($request->firstName === '') {
            return Result::failure('participant.first_name_empty');
        }

        if ($request->lastName === '') {
            return Result::failure('participant.last_name_empty');
        }

        if ($request->email === '') {
            return Result::failure('participant.email_empty');
        }

        $validator = Validator::make(['email' => $request->email], [
            'email' => 'email',
        ]);

        if ($validator->fails()) {
            return Result::failure('participant.email_invalid');
        }

        if ($participant->email !== $request->email && Participant::where('email', $request->email)->exists()) {
            return Result::failure('participant.email_already_exists');
        }

        $participant->email = $request->email;
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
        } else {
            $participant->gesundheitsinformationen = null;
            $participant->gesundheitsinformationen_einwilligung = false;
        }

        $participant->save();

        return Result::success(new Response($participant->id));
    }
}
