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
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
        ]);

        $user->assignRole(Siteman::createSuperAdminRole());

        $mainMenu = Menu::create(['name' => 'Main Menu', 'is_visible' => true]);
        $mainMenu->locations()->create(['location' => 'header']);

        // Root Level Pages
        $homePage = Page::factory()
            ->published()
            ->withMarkdownBlock(true)
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

        // About Section (Level 1 with children)
        $aboutPage = Page::factory()
            ->published()
            ->withMarkdownBlock(true)
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

        // About > Team (Level 2)
        $teamPage = Page::factory()
            ->published()
            ->withMarkdownBlock(true)
            ->create([
                'title' => 'Our Team',
                'slug' => '/team',
                'author_id' => $user->id,
                'parent_id' => $aboutPage->id,
                'order' => 1,
            ]);

        // About > Team > Leadership (Level 3 - max depth)
        Page::factory()
            ->published()
            ->withMarkdownBlock(true)
            ->create([
                'title' => 'Leadership',
                'slug' => '/leadership',
                'author_id' => $user->id,
                'parent_id' => $teamPage->id,
                'order' => 1,
            ]);

        // About > Team > Developers (Level 3)
        Page::factory()
            ->published()
            ->withMarkdownBlock(true)
            ->create([
                'title' => 'Developers',
                'slug' => '/developers',
                'author_id' => $user->id,
                'parent_id' => $teamPage->id,
                'order' => 2,
            ]);

        // About > History (Level 2)
        Page::factory()
            ->published()
            ->withMarkdownBlock(true)
            ->create([
                'title' => 'Company History',
                'slug' => '/history',
                'author_id' => $user->id,
                'parent_id' => $aboutPage->id,
                'order' => 2,
            ]);

        // About > Contact (Level 2)
        Page::factory()
            ->published()
            ->withMarkdownBlock(true)
            ->create([
                'title' => 'Contact Us',
                'slug' => '/contact',
                'author_id' => $user->id,
                'parent_id' => $aboutPage->id,
                'order' => 3,
            ]);

        // Products Section (Level 1 with children)
        $productsPage = Page::factory()
            ->published()
            ->withMarkdownBlock(true)
            ->create([
                'title' => 'Products',
                'slug' => '/products',
                'author_id' => $user->id,
                'order' => 3,
            ]);
        $mainMenu->menuItems()->create([
            'title' => 'Products',
            'linkable_type' => Page::class,
            'linkable_id' => $productsPage->id,
            'order' => 3,
        ]);

        // Products > Software (Level 2)
        $softwarePage = Page::factory()
            ->published()
            ->withMarkdownBlock(true)
            ->create([
                'title' => 'Software Solutions',
                'slug' => '/software',
                'author_id' => $user->id,
                'parent_id' => $productsPage->id,
                'order' => 1,
            ]);

        // Products > Software > CMS (Level 3)
        Page::factory()
            ->published()
            ->withMarkdownBlock(true)
            ->create([
                'title' => 'Content Management',
                'slug' => '/cms',
                'author_id' => $user->id,
                'parent_id' => $softwarePage->id,
                'order' => 1,
            ]);

        // Products > Software > Analytics (Level 3)
        Page::factory()
            ->published()
            ->withMarkdownBlock(true)
            ->create([
                'title' => 'Analytics Platform',
                'slug' => '/analytics',
                'author_id' => $user->id,
                'parent_id' => $softwarePage->id,
                'order' => 2,
            ]);

        // Products > Hardware (Level 2)
        Page::factory()
            ->published()
            ->withMarkdownBlock(true)
            ->create([
                'title' => 'Hardware',
                'slug' => '/hardware',
                'author_id' => $user->id,
                'parent_id' => $productsPage->id,
                'order' => 2,
            ]);

        // Documentation Section (Level 1 with extensive children)
        $docsPage = Page::factory()
            ->published()
            ->withMarkdownBlock(true)
            ->create([
                'title' => 'Documentation',
                'slug' => '/docs',
                'author_id' => $user->id,
                'order' => 4,
            ]);
        $mainMenu->menuItems()->create([
            'title' => 'Documentation',
            'linkable_type' => Page::class,
            'linkable_id' => $docsPage->id,
            'order' => 4,
        ]);

        // Docs > Getting Started (Level 2)
        $gettingStartedPage = Page::factory()
            ->published()
            ->withMarkdownBlock(true)
            ->create([
                'title' => 'Getting Started',
                'slug' => '/getting-started',
                'author_id' => $user->id,
                'parent_id' => $docsPage->id,
                'order' => 1,
            ]);

        // Docs > Getting Started > Installation (Level 3)
        Page::factory()
            ->published()
            ->withMarkdownBlock(true)
            ->create([
                'title' => 'Installation',
                'slug' => '/installation',
                'author_id' => $user->id,
                'parent_id' => $gettingStartedPage->id,
                'order' => 1,
            ]);

        // Docs > Getting Started > Quick Start (Level 3)
        Page::factory()
            ->published()
            ->withMarkdownBlock(true)
            ->create([
                'title' => 'Quick Start Guide',
                'slug' => '/quick-start',
                'author_id' => $user->id,
                'parent_id' => $gettingStartedPage->id,
                'order' => 2,
            ]);

        // Docs > Configuration (Level 2)
        $configPage = Page::factory()
            ->published()
            ->withMarkdownBlock(true)
            ->create([
                'title' => 'Configuration',
                'slug' => '/configuration',
                'author_id' => $user->id,
                'parent_id' => $docsPage->id,
                'order' => 2,
            ]);

        // Docs > Configuration > Database (Level 3)
        Page::factory()
            ->published()
            ->withMarkdownBlock(true)
            ->create([
                'title' => 'Database Setup',
                'slug' => '/database',
                'author_id' => $user->id,
                'parent_id' => $configPage->id,
                'order' => 1,
            ]);

        // Docs > Configuration > Environment (Level 3)
        Page::factory()
            ->published()
            ->withMarkdownBlock(true)
            ->create([
                'title' => 'Environment Variables',
                'slug' => '/environment',
                'author_id' => $user->id,
                'parent_id' => $configPage->id,
                'order' => 2,
            ]);

        // Docs > API Reference (Level 2)
        $apiPage = Page::factory()
            ->published()
            ->withMarkdownBlock(true)
            ->create([
                'title' => 'API Reference',
                'slug' => '/api',
                'author_id' => $user->id,
                'parent_id' => $docsPage->id,
                'order' => 3,
            ]);

        // Docs > API Reference > Authentication (Level 3)
        Page::factory()
            ->published()
            ->withMarkdownBlock(true)
            ->create([
                'title' => 'Authentication',
                'slug' => '/authentication',
                'author_id' => $user->id,
                'parent_id' => $apiPage->id,
                'order' => 1,
            ]);

        // Docs > API Reference > Endpoints (Level 3)
        Page::factory()
            ->published()
            ->withMarkdownBlock(true)
            ->create([
                'title' => 'API Endpoints',
                'slug' => '/endpoints',
                'author_id' => $user->id,
                'parent_id' => $apiPage->id,
                'order' => 2,
            ]);

        // Blog Section (Level 1 with blog_index type)
        $blogIndexPage = Page::factory()
            ->published()
            ->create([
                'title' => 'Blog',
                'slug' => '/blog',
                'type' => 'blog_index',
                'author_id' => $user->id,
                'order' => 5,
            ]);
        $mainMenu->menuItems()->create([
            'title' => 'Blog',
            'linkable_type' => Page::class,
            'linkable_id' => $blogIndexPage->id,
            'order' => 5,
        ]);

        // Create realistic blog posts under blog index
        Page::factory()
            ->published()
            ->withMarkdownBlock()
            ->withTags(['laravel', 'php', 'cms'])
            ->create([
                'title' => 'Introducing Siteman CMS',
                'slug' => '/introducing-siteman',
                'parent_id' => $blogIndexPage->id,
                'author_id' => $user->id,
            ]);

        Page::factory()
            ->published()
            ->withMarkdownBlock()
            ->withTags(['tutorial', 'beginner'])
            ->create([
                'title' => 'Building Your First Page',
                'slug' => '/building-first-page',
                'parent_id' => $blogIndexPage->id,
                'author_id' => $user->id,
            ]);

        Page::factory()
            ->published()
            ->withMarkdownBlock()
            ->withTags(['advanced', 'customization'])
            ->create([
                'title' => 'Advanced Customization Techniques',
                'slug' => '/advanced-customization',
                'parent_id' => $blogIndexPage->id,
                'author_id' => $user->id,
            ]);

        Page::factory()
            ->published()
            ->withMarkdownBlock()
            ->withTags(['performance', 'optimization'])
            ->create([
                'title' => 'Performance Optimization Tips',
                'slug' => '/performance-tips',
                'parent_id' => $blogIndexPage->id,
                'author_id' => $user->id,
            ]);

        Page::factory()
            ->published()
            ->withMarkdownBlock()
            ->withTags(['security', 'best-practices'])
            ->create([
                'title' => 'Security Best Practices',
                'slug' => '/security-practices',
                'parent_id' => $blogIndexPage->id,
                'author_id' => $user->id,
            ]);

        // Utility Pages (Level 1)
        $tagIndexPage = Page::factory()
            ->published()
            ->create([
                'title' => 'Tags',
                'slug' => '/tags',
                'author_id' => $user->id,
                'type' => 'tag_index',
                'order' => 6,
            ]);

        Page::factory()
            ->published()
            ->create([
                'title' => 'RSS Feed',
                'slug' => '/rss',
                'author_id' => $user->id,
                'type' => 'rss_feed',
                'order' => 7,
            ]);

        Page::factory()
            ->published()
            ->withMarkdownBlock(true)
            ->create([
                'title' => 'Privacy Policy',
                'slug' => '/privacy',
                'author_id' => $user->id,
                'order' => 9,
            ]);

        Page::factory()
            ->published()
            ->withMarkdownBlock(true)
            ->create([
                'title' => 'Terms of Service',
                'slug' => '/terms',
                'author_id' => $user->id,
                'order' => 10,
            ]);
    }
}
