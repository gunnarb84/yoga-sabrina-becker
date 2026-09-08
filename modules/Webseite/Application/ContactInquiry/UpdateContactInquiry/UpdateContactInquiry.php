<?php

declare(strict_types=1);

namespace Yoga\Modules\Webseite\Application\ContactInquiry\UpdateContactInquiry;

use Yoga\Modules\Webseite\Domain\ContactInquiry\ContactInquiry;
use Yoga\Modules\Webseite\Domain\ContactInquiry\ContactInquiryStatus;
use Yoga\Platform\Shared\Application\Result;

final readonly class UpdateContactInquiry
{
    /** @return Result<Response> */
    public function execute(Request $request): Result
    {
        $status = ContactInquiryStatus::tryFrom($request->status);

        if ($status === null) {
            return Result::failure('contact_inquiry.invalid_status');
        }

        $inquiry = ContactInquiry::findById($request->inquiryId);

        if ($inquiry === null) {
            return Result::failure('contact_inquiry.not_found');
        }

        $inquiry->aendere($status, $request->note);
        $inquiry->save();

        return Result::success(new Response($inquiry->id));
    }
}
