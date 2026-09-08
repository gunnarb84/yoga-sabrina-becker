<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\OutboundMessage\SendOutboundMessage;

use Illuminate\Support\Facades\Mail;
use Throwable;
use Yoga\Modules\Verwaltung\Application\Mail\HtmlAttachmentMail;
use Yoga\Modules\Verwaltung\Domain\OutboundMessage\OutboundMessage;
use Yoga\Modules\Verwaltung\Domain\OutboundMessage\OutboundMessageStatus;
use Yoga\Platform\Shared\Application\Result;

final readonly class SendOutboundMessage
{
    /**
     * Sendet eine E-Mail und protokolliert sie als ausgehende Nachricht mit
     * Endstatus (versandt oder fehlgeschlagen, samt Fehlerinformation).
     *
     * @return Result<Response>
     */
    public function execute(Request $request): Result
    {
        $message = new OutboundMessage([
            'anmeldung_id' => $request->anmeldungId,
            'empfaenger' => $request->recipient,
            'betreff' => $request->subject,
            'inhalt' => $request->html,
            'status' => OutboundMessageStatus::Pending->value,
        ]);
        $message->save();

        try {
            $mail = new HtmlAttachmentMail($request->subject, $request->html, $request->attachment);

            Mail::to($request->recipient)->send($mail);
            $message->markSent();
            $message->save();
        } catch (Throwable $e) {
            $message->markFailed($e->getMessage());
            $message->save();
        }

        return Result::success(new Response($message->id));
    }
}
