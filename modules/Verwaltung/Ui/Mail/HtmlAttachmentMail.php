<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Ui\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class HtmlAttachmentMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @param  array{filename: string, content: string, mime: string}|null  $attachment
     */
    public function __construct(
        public string $subjectText,
        public string $htmlContent,
        public ?array $attachment = null,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectText,
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: $this->htmlContent,
        );
    }

    /**
     * @return list\Attachment
     */
    public function attachments(): array
    {
        if ($this->attachment === null) {
            return [];
        }

        return [
            Attachment::fromData(fn (): string => $this->attachment['content'], $this->attachment['filename'])
                ->withMime($this->attachment['mime']),
        ];
    }
}
