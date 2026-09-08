<?php

declare(strict_types=1);

namespace Yoga\Modules\Webseite\Application\ContactInquiry\RecordContactInquiry;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Yoga\Modules\Verwaltung\Application\OutboundMessage\SendOutboundMessage\Request as SendMailRequest;
use Yoga\Modules\Verwaltung\Application\OutboundMessage\SendOutboundMessage\SendOutboundMessage as MailSender;
use Yoga\Modules\Webseite\Domain\ContactInquiry\ContactInquiry;
use Yoga\Modules\Webseite\Domain\ContactInquiry\ContactInquiryStatus;
use Yoga\Platform\Shared\Application\Result;

final readonly class RecordContactInquiry
{
    /**
     * Höchstens fünf Anfragen innerhalb von zehn Minuten je IP-Adresse.
     */
    private const MAX_PER_WINDOW = 5;

    /**
     * Zeitfenster der Ratenbegrenzung in Sekunden.
     */
    private const WINDOW_SECONDS = 600;

    public function __construct(private MailSender $mailSender)
    {
    }

    /** @return Result<Response> */
    public function execute(Request $request): Result
    {
        $validator = Validator::make([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'topic' => $request->topic,
            'message' => $request->message,
        ], [
            'name' => ['required', 'string', 'max:200'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'topic' => ['nullable', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        if ($validator->fails()) {
            $emailFails = $validator->errors()->has('email');

            return Result::failure(
                $emailFails ? 'contact_inquiry.invalid_email' : 'contact_inquiry.invalid_input',
                array_values($validator->errors()->all()),
            );
        }

        // Ausgefülltes Honeypot-Feld: Anfrage verwerfen, aber wie Erfolg antworten.
        if ($request->honeypot !== '') {
            return Result::success(new Response(null));
        }

        $limiterKey = 'contact-inquiry:'.$request->ipAddress;

        if (RateLimiter::tooManyAttempts($limiterKey, self::MAX_PER_WINDOW)) {
            return Result::failure('contact_inquiry.rate_limited');
        }

        $inquiry = new ContactInquiry([
            'name' => $request->name,
            'email' => $request->email,
            'telefon' => $request->phone,
            'anlass' => $request->topic,
            'nachricht' => $request->message,
            'status' => ContactInquiryStatus::New->value,
            'empfangen_am' => now(),
        ]);
        $inquiry->save();

        RateLimiter::hit($limiterKey, self::WINDOW_SECONDS);

        $this->sendEmails($inquiry);

        return Result::success(new Response($inquiry->id));
    }

    private function sendEmails(ContactInquiry $inquiry): void
    {
        $acknowledgementHtml = view('emails.contact-inquiry-acknowledgement', [
            'name' => $inquiry->name,
        ])->render();

        $this->mailSender->execute(new SendMailRequest(
            recipient: $inquiry->email,
            subject: 'Eingangsbestätigung Ihrer Anfrage',
            html: $acknowledgementHtml,
        ));

        $notificationHtml = view('emails.contact-inquiry-notification', [
            'inquiry' => $inquiry,
        ])->render();

        $this->mailSender->execute(new SendMailRequest(
            recipient: config()->string('mail.contact_inquiry_recipient'),
            subject: 'Neue Kontaktanfrage von '.$inquiry->name,
            html: $notificationHtml,
        ));
    }
}
