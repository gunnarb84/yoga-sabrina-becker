<?php

declare(strict_types=1);

namespace Yoga\Modules\Webseite\Application\Page;

use Yoga\Modules\Webseite\Domain\Page\Page;

final readonly class GetPageContent
{
    public function execute(string $slug): ?object
    {
        $page = Page::where('slug', $slug)
            ->where('veroeffentlicht', true)
            ->first();

        if ($page === null) {
            return null;
        }

        return (object) [
            'slug' => $page->slug,
            'titel' => $page->titel,
            'inhalt' => $page->inhalt,
            'meta_beschreibung' => $page->meta_beschreibung,
        ];
    }
}
