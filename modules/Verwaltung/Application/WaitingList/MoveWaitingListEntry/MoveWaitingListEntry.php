<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\WaitingList\MoveWaitingListEntry;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Yoga\Modules\Verwaltung\Domain\Registration\Registration;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationStatus;
use Yoga\Modules\Verwaltung\Domain\WaitingList\WaitingList;
use Yoga\Platform\Shared\Application\Result;

final readonly class MoveWaitingListEntry
{
    /** @return Result<Response> */
    public function execute(Request $request): Result
    {
        $registration = Registration::findById($request->registrationId);

        if ($registration === null || $registration->status !== RegistrationStatus::WaitingList) {
            return Result::failure('waiting_list.entry_not_found');
        }

        if (! in_array($request->direction, ['up', 'down'], true)) {
            return Result::failure('waiting_list.invalid_direction');
        }

        return DB::transaction(function () use ($registration, $request): Result {
            $activityIdBytes = Uuid::fromString($registration->aktivitaet_id)->getBytes();

            /** @var list<WaitingList> $entries */
            $entries = WaitingList::whereHas('anmeldung', function (Builder $query) use ($activityIdBytes): void {
                $query->whereRaw('aktivitaet_id = ?', [$activityIdBytes])
                    ->where('status', RegistrationStatus::WaitingList->value);
            })
                ->orderBy('rang')
                ->get();

            $currentIndex = null;
            foreach ($entries as $index => $entry) {
                if ($entry->anmeldung_id === $registration->id) {
                    $currentIndex = $index;
                }
            }

            if ($currentIndex === null) {
                return Result::failure('waiting_list.entry_not_found');
            }

            $swapIndex = $request->direction === 'up' ? $currentIndex - 1 : $currentIndex + 1;

            if (! isset($entries[$swapIndex])) {
                return Result::failure('waiting_list.cannot_move_further');
            }

            $currentEntry = $entries[$currentIndex];
            $swapEntry = $entries[$swapIndex];

            $temp = $currentEntry->rang;
            $currentEntry->rang = $swapEntry->rang;
            $swapEntry->rang = $temp;

            $currentEntry->save();
            $swapEntry->save();

            return Result::success(new Response($registration->id));
        });
    }
}
