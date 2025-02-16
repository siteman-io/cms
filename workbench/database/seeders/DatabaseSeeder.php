<?php

namespace Workbench\Database\Seeders;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Siteman\Cms\Models\Menu;
use Siteman\Cms\Models\Page;
use Siteman\Cms\Models\Post;
use Workbench\App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
        ]);

        $role = FilamentShield::createRole();
        $user->assignRole($role);

        $mainMenu = Menu::create(['name' => 'Main Menu', 'is_visible' => true]);
        $mainMenu->locations()->create(['location' => 'header']);

        $page = Page::factory()->published()->create(['title' => 'home', 'slug' => '/', 'author_id' => $user->id]);
        $mainMenu->menuItems()->create([
            'title' => 'Home',
            'linkable_type' => Page::class,
            'linkable_id' => $page->id,
            'order' => 1,
        ]);
        $mainMenu->menuItems()->create([
            'title' => 'Blog',
            'url' => '/blog',
            'order' => 2,
        ]);
        //        Page::factory()->count(10)->published()->create();
        //        Post::factory()->count(10)->published()->create();
    }
}
