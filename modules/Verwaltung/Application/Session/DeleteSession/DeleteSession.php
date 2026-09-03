<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Session\DeleteSession;

use Yoga\Modules\Verwaltung\Domain\Session\Session;
use Yoga\Platform\Shared\Application\Result;

final readonly class DeleteSession
{
    /** @return Result<Response> */
    public function execute(Request $request): Result
    {
        $session = Session::findById($request->sessionId);

        if ($session === null) {
            return Result::failure('session.not_found');
        }

        $session->delete();

        return Result::success(new Response($request->sessionId));
    }
}
