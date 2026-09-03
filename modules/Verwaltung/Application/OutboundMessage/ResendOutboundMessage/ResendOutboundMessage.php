<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\OutboundMessage\ResendOutboundMessage;

use Yoga\Modules\Verwaltung\Domain\OutboundMessage\OutboundMessage;
use Yoga\Modules\Verwaltung\Domain\OutboundMessage\OutboundMessageStatus;
use Yoga\Platform\Shared\Application\Result;

final readonly class ResendOutboundMessage
{
    public function execute(Request $request): Result
    {
        $original = OutboundMessage::findById($request->messageId);

        if ($original === null) {
            return Result::failure('message.not_found');
        }

        if (! in_array($original->status, [OutboundMessageStatus::Failed, OutboundMessageStatus::Pending], true)) {
            return Result::failure('message.cannot_resend');
        }

        $resend = new OutboundMessage([
            'anmeldung_id' => $original->getAttribute('anmeldung_id'),
            'empfaenger' => $original->empfaenger,
            'betreff' => $original->betreff,
            'inhalt' => $original->inhalt,
            'status' => OutboundMessageStatus::Pending->value,
        ]);

        $resend->save();

        return Result::success(new Response($resend->getAttribute('id')));
    }
}
