<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Ui\CourseTemplate;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Verwaltung\Application\CourseTemplate\DeleteCourseTemplate\DeleteCourseTemplate as DeleteCourseTemplateOperation;
use Yoga\Modules\Verwaltung\Application\CourseTemplate\DeleteCourseTemplate\Request as DeleteCourseTemplateRequest;
use Yoga\Modules\Verwaltung\Application\CourseTemplate\UpdateCourseTemplate\Request as UpdateCourseTemplateRequest;
use Yoga\Modules\Verwaltung\Application\CourseTemplate\UpdateCourseTemplate\UpdateCourseTemplate as UpdateCourseTemplateOperation;
use Yoga\Modules\Verwaltung\Domain\CourseTemplate\CourseTemplate;

#[Layout('verwaltung::layouts.app')]
final class EditCourseTemplate extends Component
{
    public string $templateId = '';

    public string $title = '';

    public string $weekday = 'monday';

    public string $startTime = '';

    public int $durationMinutes = 60;

    public int $sessionCount = 8;

    public string $price = '';

    public int $maxParticipants = 10;

    public string $shortDescription = '';

    public string $longDescription = '';

    public string $location = '';

    public string $message = '';

    public bool $saved = false;

    public function mount(string $id): void
    {
        $template = CourseTemplate::findById($id);

        if ($template === null) {
            abort(404);
        }

        $this->templateId = $template->id;
        $this->title = $template->titel;
        $this->weekday = $template->wochentag;
        $this->startTime = substr($template->startzeit, 0, 5);
        $this->durationMinutes = $template->dauer_minuten;
        $this->sessionCount = $template->anzahl_termine;
        $this->price = $template->preis;
        $this->maxParticipants = $template->maximale_teilnehmerzahl;
        $this->shortDescription = $template->kurzbeschreibung ?? '';
        $this->longDescription = $template->langbeschreibung ?? '';
        $this->location = $template->ort ?? '';
    }

    public function save(UpdateCourseTemplateOperation $operation): void
    {
        $this->message = '';
        $this->saved = false;

        $result = $operation->execute(new UpdateCourseTemplateRequest(
            templateId: $this->templateId,
            title: $this->title,
            weekday: $this->weekday,
            startTime: $this->startTime,
            durationMinutes: $this->durationMinutes,
            sessionCount: $this->sessionCount,
            price: $this->price,
            maxParticipants: $this->maxParticipants,
            shortDescription: $this->shortDescription === '' ? null : $this->shortDescription,
            longDescription: $this->longDescription === '' ? null : $this->longDescription,
            location: $this->location === '' ? null : $this->location,
        ));

        if ($result->isFailure()) {
            $this->message = 'Fehler beim Speichern: ' . json_encode($result->error());

            return;
        }

        $this->saved = true;
    }

    public function delete(DeleteCourseTemplateOperation $operation): void
    {
        $this->message = '';

        $result = $operation->execute(new DeleteCourseTemplateRequest($this->templateId));

        if ($result->isFailure()) {
            $this->message = 'Fehler beim Loeschen: ' . json_encode($result->error());

            return;
        }

        $this->redirectRoute('verwaltung.course-templates');
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('verwaltung::CourseTemplate.edit-course-template', [
            'weekdayOptions' => $this->weekdayOptions(),
        ]);
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    private function weekdayOptions(): array
    {
        return [
            ['value' => 'monday', 'label' => 'Montag'],
            ['value' => 'tuesday', 'label' => 'Dienstag'],
            ['value' => 'wednesday', 'label' => 'Mittwoch'],
            ['value' => 'thursday', 'label' => 'Donnerstag'],
            ['value' => 'friday', 'label' => 'Freitag'],
            ['value' => 'saturday', 'label' => 'Samstag'],
            ['value' => 'sunday', 'label' => 'Sonntag'],
        ];
    }
}
