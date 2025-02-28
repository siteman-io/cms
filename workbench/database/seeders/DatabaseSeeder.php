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

        $page = Page::factory()
            ->published()
            ->withMarkdownBlock(true)
            ->create(['title' => 'home', 'slug' => '/', 'author_id' => $user->id]);
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
        $aboutMe = Page::factory()
            ->published()
            ->withMarkdownBlock(true)
            ->create(['title' => 'About Me', 'slug' => '/about-me', 'author_id' => $user->id]);
        $docs = Page::factory()
            ->published()
            ->withMarkdownBlock(true)
            ->create(['title' => 'Documentation', 'slug' => '/docs', 'author_id' => $user->id]);
        Page::factory()
            ->published()
            ->withMarkdownBlock(true)
            ->create(['title' => 'Getting started', 'slug' => '/getting-started', 'author_id' => $user->id, 'parent_id' => $docs->id]);
        Page::factory()
            ->published()
            ->withMarkdownBlock(true)
            ->create(['title' => 'Configuration', 'slug' => '/configuration', 'author_id' => $user->id, 'parent_id' => $docs->id]);
        Page::factory()
            ->published()
            ->withMarkdownBlock(true)
            ->create(['title' => 'foo', 'slug' => '/foo', 'author_id' => $user->id, 'parent_id' => $docs->id]);
        Page::factory()
            ->published()
            ->withMarkdownBlock(true)
            ->create(['title' => 'bar', 'slug' => '/bar', 'author_id' => $user->id, 'parent_id' => $docs->id]);
        Page::factory()->count(10)->has(Page::factory()->count(5), 'children')->published()->create();
        //        Post::factory()->count(10)->published()->create();
    }
}
