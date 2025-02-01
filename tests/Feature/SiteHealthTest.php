<?php

use Spatie\Health\Checks\Checks\CacheCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Checks\Checks\OptimizedAppCheck;
use Spatie\Health\Checks\Checks\ScheduleCheck;
use Spatie\Health\Facades\Health;

it('registers the checks properly', function () {
    expect(Health::registeredChecks()->whereInstanceOf(EnvironmentCheck::class))
        ->toHaveCount(1)
        ->and(Health::registeredChecks()->whereInstanceOf(OptimizedAppCheck::class))
        ->toHaveCount(1)
        ->and(Health::registeredChecks()->whereInstanceOf(ScheduleCheck::class))
        ->toHaveCount(1)
        ->and(Health::registeredChecks()->whereInstanceOf(CacheCheck::class))
        ->toHaveCount(1);
});

it('runs registers the scheduler correctly', function () {
    expect($events = \Illuminate\Support\Facades\Schedule::events())
        ->not()
        ->toBeEmpty()
        ->and($events[0]->command)
        ->toContain('health:check');
});

it('needs permission to view site health page', function () {})->todo();

it('needs permission to execute site health check', function () {})->todo();
it('can execute the site health check', function () {})->todo();

it('shows when the last check was executed', function () {})->todo();
