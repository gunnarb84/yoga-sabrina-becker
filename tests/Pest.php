<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

$root = dirname(__DIR__);

uses(
    TestCase::class,
    RefreshDatabase::class,
)->in(
    $root.'/src/tests/Feature',
    $root.'/src/tests/Unit',
    $root.'/modules/Verwaltung/tests',
    $root.'/modules/Webseite/tests',
);

expect()->extend('toBeUuidString', function (): void {
    $this->toBeString();
    Uuid::fromString($this->value);
});
