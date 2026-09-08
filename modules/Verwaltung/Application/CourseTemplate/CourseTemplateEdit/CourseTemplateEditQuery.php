<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\CourseTemplate\CourseTemplateEdit;

use Yoga\Modules\Verwaltung\Domain\CourseTemplate\CourseTemplate;

final readonly class CourseTemplateEditQuery
{
    /**
     * @return object{id: string, titel: string, wochentag: string, startzeit: string, dauer_minuten: int, anzahl_termine: int, preis: string, maximale_teilnehmerzahl: int, kurzbeschreibung: string, langbeschreibung: string, ort: string}|null
     */
    public function execute(string $templateId): ?object
    {
        $template = CourseTemplate::findById($templateId);

        if ($template === null) {
            return null;
        }

        return (object) [
            'id' => $template->id,
            'titel' => $template->titel,
            'wochentag' => $template->wochentag,
            'startzeit' => substr($template->startzeit, 0, 5),
            'dauer_minuten' => $template->dauer_minuten,
            'anzahl_termine' => $template->anzahl_termine,
            'preis' => $template->preis,
            'maximale_teilnehmerzahl' => $template->maximale_teilnehmerzahl,
            'kurzbeschreibung' => $template->kurzbeschreibung ?? '',
            'langbeschreibung' => $template->langbeschreibung ?? '',
            'ort' => $template->ort ?? '',
        ];
    }
}
