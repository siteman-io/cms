<?php

use Siteman\Cms\Tests\TestCase;

uses(TestCase::class)->in('Feature', 'Browser');

function createUser(array $state = []): \Workbench\App\Models\User
{
    return \Workbench\App\Models\User::factory()->create($state);
}
