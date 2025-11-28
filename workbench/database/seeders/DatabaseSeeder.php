<?php

namespace Workbench\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Siteman\Cms\Facades\Siteman;
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
            'name' => 'Jane Doe',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
        ]);

        $user->assignRole(Siteman::createSuperAdminRole());

        $mainMenu = Menu::create(['name' => 'Main Menu', 'is_visible' => true]);
        $mainMenu->locations()->create(['location' => 'header']);

        // Home Page
        $homePage = Page::factory()
            ->published()
            ->withMarkdownBlock(false, [
                "# Welcome to My Blog\n",
                "I'm a software developer passionate about building great products and sharing what I learn along the way.",
                "## What You'll Find Here\n",
                "I write about programming, technology, and the occasional life update. Whether you're here to learn something new or just curious about my latest projects, I hope you find something valuable.",
                'Feel free to explore my [blog posts](/blog) or learn more [about me](/about).',
            ])
            ->create([
                'title' => 'Home',
                'slug' => '/',
                'author_id' => $user->id,
                'order' => 1,
            ]);
        $mainMenu->menuItems()->create([
            'title' => 'Home',
            'linkable_type' => Page::class,
            'linkable_id' => $homePage->id,
            'order' => 1,
        ]);

        // About Page
        $aboutPage = Page::factory()
            ->published()
            ->withMarkdownBlock(false, [
                "# About Me\n",
                "Hi, I'm Jane! I'm a software developer with a passion for clean code and thoughtful design.",
                "## My Journey\n",
                "I started programming in college and never looked back. Over the years, I've worked on everything from small startups to large enterprise applications.",
                "## What I Do\n",
                "Currently, I focus on web development using Laravel, Vue.js, and TypeScript. I love building tools that make developers' lives easier.",
                "## Beyond Code\n",
                "When I'm not coding, you'll find me reading, hiking, or experimenting with new recipes in the kitchen.",
                "## Get in Touch\n",
                'Feel free to reach out via [email](mailto:hello@example.com) or connect with me on [GitHub](https://github.com) and [Twitter](https://twitter.com).',
            ])
            ->create([
                'title' => 'About',
                'slug' => '/about',
                'author_id' => $user->id,
                'order' => 2,
            ]);
        $mainMenu->menuItems()->create([
            'title' => 'About',
            'linkable_type' => Page::class,
            'linkable_id' => $aboutPage->id,
            'order' => 2,
        ]);

        // Blog Index Page
        $blogIndexPage = Page::factory()
            ->published()
            ->create([
                'title' => 'Blog',
                'slug' => '/blog',
                'type' => 'blog_index',
                'author_id' => $user->id,
                'order' => 3,
            ]);
        $mainMenu->menuItems()->create([
            'title' => 'Blog',
            'linkable_type' => Page::class,
            'linkable_id' => $blogIndexPage->id,
            'order' => 3,
        ]);

        // Blog posts with realistic content using extended faker
        $blogTags = [
            ['laravel', 'php', 'web-development'],
            ['javascript', 'typescript', 'frontend'],
            ['testing', 'best-practices'],
            ['devops', 'deployment'],
            ['career', 'personal'],
            ['open-source', 'community'],
            ['database', 'performance'],
            ['api', 'rest', 'backend'],
        ];

        // Create 8 blog posts with varied tags
        foreach ($blogTags as $index => $tags) {
            Page::factory()
                ->published()
                ->withBlogPost()
                ->withTags($tags)
                ->create([
                    'parent_id' => $blogIndexPage->id,
                    'author_id' => $user->id,
                    'published_at' => now()->subDays(rand(1, 90)),
                ]);
        }

        // Projects Page
        $projectsPage = Page::factory()
            ->published()
            ->withMarkdownBlock(false, [
                "# Projects\n",
                "Here are some of the projects I've been working on.",
                "## Open Source\n",
                'I contribute to various open source projects and maintain a few of my own. Check out my [GitHub profile](https://github.com) for the latest.',
                "## Side Projects\n",
                "- **DevTools CLI** - A collection of developer productivity tools\n- **BlogEngine** - A simple static site generator\n- **TaskFlow** - A minimalist task management app",
                "## Collaborations\n",
                "I'm always open to collaborating on interesting projects. If you have an idea, let's talk!",
            ])
            ->create([
                'title' => 'Projects',
                'slug' => '/projects',
                'author_id' => $user->id,
                'order' => 4,
            ]);
        $mainMenu->menuItems()->create([
            'title' => 'Projects',
            'linkable_type' => Page::class,
            'linkable_id' => $projectsPage->id,
            'order' => 4,
        ]);

        // Tag Index Page
        Page::factory()
            ->published()
            ->create([
                'title' => 'Tags',
                'slug' => '/tags',
                'author_id' => $user->id,
                'type' => 'tag_index',
                'order' => 5,
            ]);

        // RSS Feed Page
        Page::factory()
            ->published()
            ->create([
                'title' => 'RSS Feed',
                'slug' => '/rss',
                'author_id' => $user->id,
                'type' => 'rss_feed',
                'order' => 6,
            ]);
    }
}
