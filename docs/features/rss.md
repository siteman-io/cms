---
outline: deep
---

# RSS Feeds

Siteman CMS includes built-in RSS/Atom feed generation powered by [Spatie's Laravel Feed package](https://github.com/spatie/laravel-feed). RSS feeds allow users to subscribe to your content using feed readers and enable content syndication.

## Understanding RSS Feeds in Siteman

RSS feeds in Siteman:
- Are created as special page types
- Generate XML feeds automatically from published content
- Support both RSS and Atom formats
- Include page metadata (title, author, published date, content)
- Update automatically when content changes

## Creating an RSS Feed

### Step 1: Create a Feed Page

1. Navigate to **Content** > **Pages** in the admin panel
2. Click **New Page**
3. Configure the feed:
   - **Title**: "RSS Feed" or "Blog Feed"
   - **Slug**: "/feed" or "/blog/feed"
   - **Type**: Select "RSS Feed"
   - **Feed Title**: Your feed's title (e.g., "My Blog - Latest Posts")
   - **Feed Description**: Brief description of your feed content
   - **Feed Language**: Select language (e.g., "en-US")
   - **Published At**: Select current date/time
4. Click **Create**

The RSS feed is now accessible at the slug you specified (e.g., `https://yoursite.com/feed`).

### Step 2: Verify the Feed

Visit your feed URL in a browser. You should see XML content:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
    <title>My Blog - Latest Posts</title>
    <link href="https://yoursite.com/feed" rel="self"/>
    <updated>2024-01-15T10:30:00Z</updated>
    <id>https://yoursite.com/feed</id>
    <!-- Feed items -->
</feed>
```

## Feed Configuration

### Feed Metadata

Configure feed metadata when creating/editing the RSS Feed page:

**Feed Title**: The name of your feed
```
Example: "TechBlog - Latest Articles"
Default: Uses site name from GeneralSettings
```

**Feed Description**: Brief description of your feed's content
```
Example: "Stay updated with the latest web development tutorials and tips"
Default: Uses site description from GeneralSettings
```

**Feed Language**: Language code for the feed
```
Example: "en-US", "de-DE", "es-ES"
Default: "en-US"
```

### What Gets Included

By default, the RSS feed includes all published pages with:
- Title
- URL (computed_slug)
- Summary/excerpt
- Author name
- Published date
- Last updated date

## Customizing Feed Content

### Filtering Feed Items

To create a feed for specific content (e.g., only blog posts), you need to modify the feed items source.

**Example: Blog-specific feed**

1. Create a custom page type that extends `RssFeed`:

```php
<?php

namespace App\PageTypes;

use Siteman\Cms\PageTypes\RssFeed as BaseRssFeed;
use Siteman\Cms\Models\Page as PageModel;
use Illuminate\Http\Request;
use Spatie\Feed\Feed;
use Spatie\Feed\Helpers\ResolveFeedItems;

class BlogRssFeed extends BaseRssFeed
{
    public function render(Request $request, PageModel $page)
    {
        // Get only posts from a specific blog
        $blogId = $page->getMeta('blog_id');
        $items = PageModel::where('parent_id', $blogId)
            ->published()
            ->get()
            ->map(fn($item) => $item->toFeedItem());

        $settings = app(\Siteman\Cms\Settings\GeneralSettings::class);

        return new Feed(
            $page->getMeta('feed_title', $settings->site_name),
            $items,
            $request->url(),
            'feed::atom',
            $page->getMeta('feed_description', $settings->description),
            $page->getMeta('feed_language', 'en-US'),
            '',
            'atom',
            '',
        );
    }
}
```

2. Register the custom page type in your `AppServiceProvider`:

```php
public function boot(): void
{
    app('siteman.page-types')->register(
        'blog-feed',
        \App\PageTypes\BlogRssFeed::class
    );
}
```

### Customizing Feed Items

The `Page` model includes a `toFeedItem()` method that converts pages to feed items:

```php
// src/Models/Page.php (excerpt)
public function toFeedItem(): FeedItem
{
    return FeedItem::create()
        ->id($this->slug)
        ->title($this->title)
        ->summary($this->excerpt ?? '')
        ->updated($this->updated_at)
        ->link($this->computed_slug)
        ->authorName($this->author->name ?? '');
}
```

To customize feed items, you can:

**Option 1: Add excerpt field to pages**

```php
// In a migration
Schema::table('pages', function (Blueprint $table) {
    $table->text('excerpt')->nullable();
});

// When creating/updating pages
$page->update([
    'excerpt' => 'A brief summary of the page content...',
]);
```

**Option 2: Use meta description**

```php
$page->update([
    'meta' => [
        'description' => 'Description that will appear in the feed',
    ],
]);

// Modify toFeedItem() to use meta
public function toFeedItem(): FeedItem
{
    return FeedItem::create()
        ->id($this->slug)
        ->title($this->title)
        ->summary($this->getMeta('description', ''))
        ->updated($this->updated_at)
        ->link($this->computed_slug)
        ->authorName($this->author->name ?? '');
}
```

## Multiple RSS Feeds

Create multiple feeds for different content sections:

### Main Blog Feed
- **URL**: `/blog/feed`
- **Content**: All blog posts
- **Use case**: General blog subscribers

### Tag-Specific Feeds
- **URL**: `/blog/laravel/feed`
- **Content**: Posts tagged with "Laravel"
- **Use case**: Topic-specific subscribers

### Author Feeds
- **URL**: `/author/john/feed`
- **Content**: Posts by specific author
- **Use case**: Following specific authors

## Adding Feed Discovery

Help users and feed readers discover your feeds by adding feed discovery meta tags to your theme layout:

```blade
{{-- In your theme layout head section --}}
<link rel="alternate"
      type="application/atom+xml"
      title="My Blog - Atom Feed"
      href="{{ url('/feed') }}">

<link rel="alternate"
      type="application/rss+xml"
      title="My Blog - RSS Feed"
      href="{{ url('/feed') }}">
```

## Feed Subscription Links

Add subscription links to your site:

```blade
{{-- Simple feed link --}}
<a href="/feed" class="feed-link">
    Subscribe via RSS
</a>

{{-- With icon --}}
<a href="/feed" class="feed-link">
    <svg><!-- RSS icon --></svg>
    Subscribe
</a>

{{-- Multiple format options --}}
<div class="feed-options">
    <a href="/feed">Atom Feed</a>
    <a href="/feed.rss">RSS Feed</a>
</div>
```

## Testing Your Feed

### Feed Validators

Test your feed with these validators:
- [W3C Feed Validator](https://validator.w3.org/feed/)
- [RSS Feed Validator](https://www.feedvalidator.org/)

### Feed Readers

Test with popular feed readers:
- [Feedly](https://feedly.com/)
- [Inoreader](https://www.inoreader.com/)
- [NewsBlur](https://newsblur.com/)

### Browser Testing

Most modern browsers will display formatted XML when you visit the feed URL directly.

## Feed Performance

### Caching

Consider caching RSS feeds for better performance:

```php
// In your custom RssFeed page type
use Illuminate\Support\Facades\Cache;

public function render(Request $request, PageModel $page)
{
    return Cache::remember(
        "feed:{$page->id}",
        now()->addHour(),
        function () use ($request, $page) {
            // Generate feed
            return parent::render($request, $page);
        }
    );
}
```

Clear the cache when content updates:

```php
// In your Page model
protected static function booted(): void
{
    static::saved(function ($page) {
        // Clear feed cache when a page is saved
        Cache::forget('feed:main');
    });
}
```

### Pagination

Limit feed items to improve performance:

```php
// In custom RssFeed page type
$items = PageModel::published()
    ->orderBy('published_at', 'desc')
    ->take(50) // Limit to 50 most recent items
    ->get()
    ->map(fn($item) => $item->toFeedItem());
```

## Feed Security

### Rate Limiting

Protect your feed from excessive requests:

```php
// In RouteServiceProvider or web.php
Route::middleware(['throttle:60,1'])->group(function () {
    // Siteman routes including feed
});
```

### Content Filtering

Be cautious about what content you include:
- Exclude draft or unpublished content
- Sanitize HTML in feed items
- Consider partial content vs. full content

## Advanced Feed Features

### Including Featured Images

Extend `toFeedItem()` to include images:

```php
public function toFeedItem(): FeedItem
{
    $feedItem = FeedItem::create()
        ->id($this->slug)
        ->title($this->title)
        ->summary($this->excerpt ?? '')
        ->updated($this->updated_at)
        ->link($this->computed_slug)
        ->authorName($this->author->name ?? '');

    // Add featured image if exists
    if ($this->hasMedia('featured_image')) {
        $feedItem->image($this->getFirstMediaUrl('featured_image'));
    }

    return $feedItem;
}
```

### Adding Categories

Include tags as feed categories:

```php
// Extend FeedItem to support categories
$feedItem->category($this->tags->pluck('name')->toArray());
```

### Full Content vs Excerpts

Choose between full content or excerpts:

```php
// Excerpt only (better for driving traffic)
->summary($this->excerpt)

// Full content (better for readers)
->summary($this->content)

// Hybrid approach
->summary($this->excerpt)
->content($this->content) // If FeedItem supports it
```

## Troubleshooting

### Feed Returns 404

1. Verify the page exists and is published
2. Check the slug is correct
3. Verify route registration: `php artisan route:list | grep feed`
4. Clear route cache: `php artisan route:clear`

### Feed Shows Empty

1. Ensure pages are published (`published_at` in the past)
2. Check `getFeedItems()` method returns items
3. Verify pages have required fields (title, slug, published_at)

### Feed Validation Errors

1. Check XML is well-formed
2. Ensure dates are in ISO 8601 format
3. Verify URLs are absolute, not relative
4. Check for special characters that need escaping

### Feed Not Updating

1. Clear cache: `php artisan cache:clear`
2. Check if caching is aggressive
3. Verify `updated_at` is being set on page updates

## SEO and RSS Feeds

Benefits of RSS feeds for SEO:
- **Content Distribution**: Wider content reach
- **Backlinks**: Feed aggregators may link back
- **Freshness**: Signals regular content updates
- **Indexing**: Helps search engines discover new content

Best practices:
- Include full or substantial excerpts
- Use descriptive feed titles and descriptions
- Keep feed URLs permanent (don't change)
- Submit feeds to feed directories

## Related Features

- [Blog](/features/blog) - Create blog content for your feed
- [Tags](/features/tags) - Organize feed content by tags
- [Page Types](/features/page-types) - Understanding RSS Feed page type
- [Settings](/features/settings) - Configure site-wide feed defaults
