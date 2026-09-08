<?php

declare(strict_types=1);

namespace Yoga\Modules\Webseite\Application\Navigation;

use Yoga\Modules\Webseite\Domain\Navigation\NavigationItem;

final readonly class ListNavigationItems
{
    /**
     * @return list<object>
     */
    public function execute(): array
    {
        $items = [];

        foreach (NavigationItem::where('aktiv', true)->orderBy('sortierung')->get() as $item) {
            $items[] = (object) [
                'bezeichnung' => $item->bezeichnung,
                'url' => $item->url,
                'extern' => $item->extern,
            ];
        }

        return $items;
    }
}
