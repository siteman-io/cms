<?php

namespace Workbench\Database\Seeders;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Siteman\Cms\Models\Menu;
use Siteman\Cms\Models\Page;
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

        $homePage = Page::factory()
            ->published()
            ->withMarkdownBlock(true)
            ->create(['title' => 'home', 'slug' => '/', 'author_id' => $user->id]);
        $mainMenu->menuItems()->create([
            'title' => 'Home',
            'linkable_type' => Page::class,
            'linkable_id' => $homePage->id,
            'order' => 1,
        ]);
        $blogIndexPage = Page::factory()
            ->published()
            ->create(['title' => 'Blog', 'slug' => '/blog', 'type' => 'blog_index', 'author_id' => $user->id]);
        $mainMenu->menuItems()->create([
            'title' => 'Blog',
            'linkable_type' => Page::class,
            'linkable_id' => $blogIndexPage->id,
            'order' => 2,
        ]);
        Page::factory()->count(10)->published()->withMarkdownBlock()->withTags(['foo', 'bar', 'baz'])->create(['parent_id' => $blogIndexPage->id, 'author_id' => $user->id]);
        $tagIndexPage = Page::factory()
            ->published()
            ->create(['title' => 'Tags', 'slug' => '/tags', 'author_id' => $user->id, 'type' => 'tag_index']);
        Page::factory()
            ->published()
            ->create(['title' => 'RSS', 'slug' => '/rss', 'author_id' => $user->id, 'type' => 'rss_feed']);
        $mainMenu->menuItems()->create([
            'title' => 'Blog',
            'linkable_type' => Page::class,
            'linkable_id' => $tagIndexPage->id,
            'order' => 3,
        ]);
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
    }
}
