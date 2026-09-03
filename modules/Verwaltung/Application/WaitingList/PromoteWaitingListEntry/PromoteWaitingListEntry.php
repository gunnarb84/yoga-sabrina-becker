<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\WaitingList\PromoteWaitingListEntry;

use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Yoga\Modules\Verwaltung\Application\Registration\SendRegistrationConfirmation\Request as SendConfirmationRequest;
use Yoga\Modules\Verwaltung\Application\Registration\SendRegistrationConfirmation\SendRegistrationConfirmation;
use Yoga\Modules\Verwaltung\Domain\Registration\Registration;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationStatus;
use Yoga\Modules\Verwaltung\Domain\WaitingList\WaitingList;
use Yoga\Platform\Shared\Application\Result;

final readonly class PromoteWaitingListEntry
{
    public function __construct(private SendRegistrationConfirmation $confirmation)
    {
    }

    /** @return Result<Response> */
    public function execute(Request $request): Result
    {
        $registration = Registration::findById($request->registrationId);

        if ($registration === null || $registration->status !== RegistrationStatus::WaitingList) {
            return Result::failure('waiting_list.entry_not_found');
        }

        return DB::transaction(function () use ($registration): Result {
            $entry = WaitingList::whereRaw('anmeldung_id = ?', [Uuid::fromString($registration->id)->getBytes()])->first();

            if ($entry === null) {
                return Result::failure('waiting_list.entry_not_found');
            }

            $entry->promote();
            $entry->save();

            $registration->confirm();
            $registration->save();

            $this->confirmation->execute(new SendConfirmationRequest($registration->id));

            return Result::success(new Response($registration->id));
        });
    }
}
