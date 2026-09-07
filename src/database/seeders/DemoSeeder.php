<?php

declare(strict_types=1);

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Yoga\Modules\Verwaltung\Domain\Activity\Activity;
use Yoga\Modules\Verwaltung\Domain\Activity\ActivityStatus;
use Yoga\Modules\Verwaltung\Domain\Activity\ActivityType;
use Yoga\Modules\Verwaltung\Domain\Session\Session;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->createPublishedCourse();
        $this->createDraftWorkshop();
    }

    private function createPublishedCourse(): void
    {
        $activity = Activity::updateOrCreate(
            ['slug' => 'vinyasa-flow-mittwochs'],
            [
                'typ' => ActivityType::Course->value,
                'titel' => 'Vinyasa Flow Mittwochs',
                'kurzbeschreibung' => 'Dynamischer Vinyasa-Flow für alle Level.',
                'langbeschreibung' => 'Eine wöchentliche Kursreihe mit Fokus auf Atmung, Kraft und Beweglichkeit.',
                'preis' => '90.0000',
                'waehrung' => 'EUR',
                'maximale_teilnehmerzahl' => 12,
                'veroeffentlicht' => true,
                'status' => ActivityStatus::Published->value,
            ]
        );

        if ($activity->termine()->count() > 0) {
            return;
        }

        $start = Carbon::now()->next(Carbon::WEDNESDAY)->setTime(18, 0);

        for ($i = 0; $i < 4; $i++) {
            $session = new Session([
                'aktivitaet_id' => $activity->id,
                'beginn' => $start->copy()->addWeeks($i),
                'ende' => $start->copy()->addWeeks($i)->addMinutes(75),
                'ort' => 'Studio 1',
                'hinweis' => 'Bitte eigene Matte mitbringen.',
            ]);
            $session->save();
        }
    }

    private function createDraftWorkshop(): void
    {
        $activity = Activity::updateOrCreate(
            ['slug' => 'yoga-fuer-den-ruecken'],
            [
                'typ' => ActivityType::Workshop->value,
                'titel' => 'Yoga für den Rücken',
                'kurzbeschreibung' => 'Ein ganztägiger Workshop zur Rückengesundheit.',
                'langbeschreibung' => 'Gezielte Übungen zur Stärkung und Entlastung der Wirbelsäule.',
                'preis' => '85.0000',
                'waehrung' => 'EUR',
                'maximale_teilnehmerzahl' => 8,
                'veroeffentlicht' => false,
                'status' => ActivityStatus::Draft->value,
            ]
        );

        if ($activity->termine()->count() > 0) {
            return;
        }

        $session = new Session([
            'aktivitaet_id' => $activity->id,
            'beginn' => Carbon::now()->addWeeks(2)->setTime(10, 0),
            'ende' => Carbon::now()->addWeeks(2)->setTime(16, 0),
            'ort' => 'Seminarraum',
            'hinweis' => 'Inklusive Mittagspause.',
        ]);
        $session->save();
    }
}
