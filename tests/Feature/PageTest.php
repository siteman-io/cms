<?php

use BezhanSalleh\FilamentShield\Support\Utils;
use Siteman\Cms\Models\Page;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;

it('shows only published pages', function () {
    Page::factory()
        ->create([
            'published_at' => null,
            'slug' => 'draft',
        ]);

    Page::factory()
        ->published()
        ->create([
            'slug' => 'published',
        ]);

    $this->get('/draft')->assertStatus(404);
    $this->get('/published')->assertStatus(200);
});

it('needs permission to create pages', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get('/admin/pages/create')
        ->assertForbidden();

    $permission1 = Utils::getPermissionModel()::create(['name' => 'view_any_page', 'guard' => 'web']);
    $permission2 = Utils::getPermissionModel()::create(['name' => 'create_page', 'guard' => 'web']);
    $user->givePermissionTo($permission1, $permission2);

    actingAs($user->refresh())
        ->get('/admin/pages/create')
        ->assertOk();
});

it('can create pages', function () {})->todo();

it('needs permission to update pages', function () {})->todo();
it('can update pages', function () {})->todo();

it('needs permission to delete pages', function () {})->todo();
it('can delete pages', function () {})->todo();
