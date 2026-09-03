<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Ui\CourseTemplate;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Verwaltung\Application\CourseTemplate\CourseTemplates\CourseTemplatesQuery;
use Yoga\Modules\Verwaltung\Application\CourseTemplate\DeleteCourseTemplate\DeleteCourseTemplate as DeleteCourseTemplateOperation;
use Yoga\Modules\Verwaltung\Application\CourseTemplate\DeleteCourseTemplate\Request as DeleteCourseTemplateRequest;
use Yoga\Modules\Verwaltung\Application\CourseTemplate\GenerateSessionsFromTemplate\GenerateSessionsFromTemplate as GenerateSessionsFromTemplateOperation;
use Yoga\Modules\Verwaltung\Application\CourseTemplate\GenerateSessionsFromTemplate\Request as GenerateSessionsFromTemplateRequest;

#[Layout('verwaltung::layouts.app')]
final class CourseTemplateList extends Component
{
    /**
     * @var list<object{id: string, titel: string, wochentag: string, startzeit: string, dauer_minuten: int, anzahl_termine: int, preis: string, maximale_teilnehmerzahl: int, ort: string|null}>
     */
    public array $templates = [];

    public string $message = '';

    public ?string $generatedActivityId = null;

    public function mount(CourseTemplatesQuery $query): void
    {
        $this->templates = $query->execute();
    }

    public function generate(string $templateId, GenerateSessionsFromTemplateOperation $operation, CourseTemplatesQuery $query): void
    {
        $this->message = '';
        $this->generatedActivityId = null;

        $result = $operation->execute(new GenerateSessionsFromTemplateRequest($templateId));

        if ($result->isFailure()) {
            $this->message = 'Fehler beim Erzeugen: ' . json_encode($result->error());

            return;
        }

        $this->generatedActivityId = $result->unwrap()->activityId;
        $this->message = 'Veranstaltung wurde aus Vorlage erzeugt und kann vor Veroeffentlichung bearbeitet werden.';
        $this->templates = $query->execute();
    }

    public function delete(string $templateId, DeleteCourseTemplateOperation $operation, CourseTemplatesQuery $query): void
    {
        $this->message = '';
        $this->generatedActivityId = null;

        $result = $operation->execute(new DeleteCourseTemplateRequest($templateId));

        if ($result->isFailure()) {
            $this->message = 'Fehler beim Loeschen: ' . json_encode($result->error());

            return;
        }

        $this->templates = $query->execute();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('verwaltung::course-template.course-template-list', [
            'weekdayLabels' => $this->weekdayLabels(),
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function weekdayLabels(): array
    {
        return [
            'monday' => 'Montag',
            'tuesday' => 'Dienstag',
            'wednesday' => 'Mittwoch',
            'thursday' => 'Donnerstag',
            'friday' => 'Freitag',
            'saturday' => 'Samstag',
            'sunday' => 'Sonntag',
        ];
    }
}
