<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Session\UpdateSession;

use Carbon\Carbon;
use Yoga\Modules\Verwaltung\Domain\Session\Session;
use Yoga\Platform\Shared\Application\Result;

final readonly class UpdateSession
{
    /** @return Result<Response> */
    public function execute(Request $request): Result
    {
        $session = Session::findById($request->sessionId);

        if ($session === null) {
            return Result::failure('session.not_found');
        }

        if ($request->startsAt === '') {
            return Result::failure('session.starts_at_empty');
        }

        if ($request->endsAt === '') {
            return Result::failure('session.ends_at_empty');
        }

        $startsAt = Carbon::parse($request->startsAt);
        $endsAt = Carbon::parse($request->endsAt);

        if (! $endsAt->isAfter($startsAt)) {
            return Result::failure('session.ends_at_not_after_starts_at');
        }

        $session->beginn = $startsAt;
        $session->ende = $endsAt;
        $session->ort = $request->location;
        $session->hinweis = $request->note;
        $session->save();

        return Result::success(new Response($session->id));
    }
}
