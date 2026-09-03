<?php

declare(strict_types=1);

uses(
    Tests\TestCase::class,
    Illuminate\Foundation\Testing\RefreshDatabase::class,
)->in('Feature', 'Unit', '../../modules/Verwaltung/tests', '../../modules/Webseite/tests');

expect()->extend('toBeUuidString', function (): void {
    $this->toBeString();
    \Ramsey\Uuid\Uuid::fromString((string) $this->value);
});
