<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Ui\CourseTemplate;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Verwaltung\Application\CourseTemplate\CreateCourseTemplate\CreateCourseTemplate as CreateCourseTemplateOperation;
use Yoga\Modules\Verwaltung\Application\CourseTemplate\CreateCourseTemplate\Request as CreateCourseTemplateRequest;

#[Layout('verwaltung::layouts.app')]
final class CreateCourseTemplate extends Component
{
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

    public bool $created = false;

    public function save(CreateCourseTemplateOperation $operation): void
    {
        $this->message = '';
        $this->created = false;

        $result = $operation->execute(new CreateCourseTemplateRequest(
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

        $this->created = true;
        $this->reset(['title', 'weekday', 'startTime', 'durationMinutes', 'sessionCount', 'price', 'maxParticipants', 'shortDescription', 'longDescription', 'location']);
        $this->weekday = 'monday';
        $this->durationMinutes = 60;
        $this->sessionCount = 8;
        $this->maxParticipants = 10;
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('verwaltung::CourseTemplate.create-course-template', [
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
