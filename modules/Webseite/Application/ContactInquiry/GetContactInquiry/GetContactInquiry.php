<?php

declare(strict_types=1);

namespace Yoga\Modules\Webseite\Application\ContactInquiry\GetContactInquiry;

use Ramsey\Uuid\Uuid;
use Yoga\Modules\Webseite\Domain\ContactInquiry\ContactInquiry;
use Yoga\Platform\Shared\Application\DbValue;

final readonly class GetContactInquiry
{
    /**
     * @return object{id: string, empfangen_am: string, name: string, email: string, telefon: string|null, anlass: string|null, nachricht: string, status: string, notiz: string|null}|null
     */
    public function execute(string $inquiryId): ?object
    {
        $inquiry = ContactInquiry::findById($inquiryId);

        if ($inquiry === null) {
            return null;
        }

        return (object) [
            'id' => Uuid::fromString($inquiry->id)->toString(),
            'empfangen_am' => DbValue::string($inquiry->empfangen_am->format('Y-m-d H:i:s')),
            'name' => DbValue::string($inquiry->name),
            'email' => DbValue::string($inquiry->email),
            'telefon' => DbValue::nullableString($inquiry->telefon),
            'anlass' => DbValue::nullableString($inquiry->anlass),
            'nachricht' => DbValue::string($inquiry->nachricht),
            'status' => DbValue::string($inquiry->status->value),
            'notiz' => DbValue::nullableString($inquiry->notiz),
        ];
    }
}
