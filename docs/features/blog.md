---
outline: deep
---

# Blog

Siteman CMS includes a powerful blogging system built on top of the hierarchical page structure. Create blog indexes and posts with support for tags, featured images, and RSS feeds.

## Understanding the Blog System

The blog system in Siteman uses a parent-child page relationship:

- **Blog Index Page**: A parent page with the `BlogIndex` type that displays a paginated list of posts
- **Blog Posts**: Child pages of the Blog Index that represent individual articles

This hierarchical structure allows you to:
- Organize posts under different blog sections
- Create multiple blogs (e.g., "News", "Updates", "Company Blog")
- Maintain a clear content hierarchy
- Generate automatic navigation and breadcrumbs

## Creating a Blog

### Step 1: Create the Blog Index Page

1. Navigate to **Content** > **Pages** in the admin panel
2. Click **New Page**
3. Configure the blog index:
   - **Title**: "Blog" (or "News", "Articles", etc.)
   - **Slug**: "/blog"
   - **Type**: Select "Blog Index"
   - **Published At**: Select current date/time
4. Click **Create**

The Blog Index page will automatically display all published posts (child pages) in reverse chronological order with pagination.

### Step 2: Create Blog Posts

1. Navigate to **Content** > **Pages**
2. Click **New Page**
3. Configure the post:
   - **Title**: Your post title
   - **Slug**: "/your-post-slug" (will become `/blog/your-post-slug`)
   - **Parent Page**: Select your "Blog" page
   - **Type**: "Page" (default)
   - **Published At**: When you want the post to go live
   - **Featured Image**: Upload an image (optional)
   - **Tags**: Add relevant tags (optional)
4. Add content using blocks (Markdown, images, etc.)
5. Click **Create**

## Blog Index Configuration

The `BlogIndex` page type automatically:

- Displays child pages (posts) in reverse chronological order
- Shows only published posts (`published_at` in the past)
- Paginates results (10 posts per page by default)
- Provides post metadata (title, excerpt, featured image, author, date)

### Customizing the Blog Index View

Create a custom view for your blog index in your theme:

```
resources/views/siteman/themes/my-theme/posts/index.blade.php
```

**Example blog index template**:

```blade
<x-my-theme::layout :page="$page">
    <div class="blog-index">
        <h1>{{ $page->title }}</h1>

        <div class="posts">
            @foreach($posts as $post)
                <article class="post-preview">
                    @if($post->hasMedia('featured_image'))
                        <img src="{{ $post->getFirstMediaUrl('featured_image', 'thumb') }}"
                             alt="{{ $post->title }}">
                    @endif

                    <h2>
                        <a href="{{ $post->computed_slug }}">
                            {{ $post->title }}
                        </a>
                    </h2>

                    <div class="meta">
                        <span class="author">By {{ $post->author->name }}</span>
                        <span class="date">{{ $post->published_at->format('F j, Y') }}</span>
                    </div>

                    @if($post->tags->isNotEmpty())
                        <div class="tags">
                            @foreach($post->tags as $tag)
                                <a href="/tags/{{ $tag->slug }}">{{ $tag->name }}</a>
                            @endforeach
                        </div>
                    @endif

                    <div class="excerpt">
                        {{ $post->getMeta('description', Str::limit(strip_tags($post->content), 150)) }}
                    </div>

                    <a href="{{ $post->computed_slug }}" class="read-more">Read more →</a>
                </article>
            @endforeach
        </div>

        {{ $posts->links() }}
    </div>
</x-my-theme::layout>
```

## Blog Post Templates

Create custom templates for individual blog posts:

```
resources/views/siteman/themes/my-theme/pages/show.blade.php
```

**Example blog post template**:

```blade
<x-my-theme::layout :page="$page">
    <article class="blog-post">
        @if($page->hasMedia('featured_image'))
            <img src="{{ $page->getFirstMediaUrl('featured_image', 'featured_image') }}"
                 alt="{{ $page->title }}"
                 class="featured-image">
        @endif

        <header>
            <h1>{{ $page->title }}</h1>

            <div class="meta">
                <span class="author">By {{ $page->author->name }}</span>
                <span class="date">{{ $page->published_at->format('F j, Y') }}</span>
                <span class="reading-time">{{ $page->getMeta('reading_time', '5 min read') }}</span>
            </div>

            @if($page->tags->isNotEmpty())
                <div class="tags">
                    @foreach($page->tags as $tag)
                        <a href="/tags/{{ $tag->slug }}" class="tag">{{ $tag->name }}</a>
                    @endforeach
                </div>
            @endif
        </header>

        <div class="content">
            @foreach($page->blocks as $block)
                @include("siteman::blocks.{$block['type']}", ['block' => $block])
            @endforeach
        </div>

        <footer>
            @if($page->parent)
                <a href="{{ $page->parent->computed_slug }}" class="back-to-index">
                    ← Back to {{ $page->parent->title }}
                </a>
            @endif
        </footer>
    </article>
</x-my-theme::layout>
```

## Featured Images

Blog posts support featured images using Spatie's Media Library:

```php
// In your controller or Livewire component
$post->addMedia($file)
    ->toMediaCollection('featured_image');

// Retrieve the image
$post->getFirstMediaUrl('featured_image'); // Full size
$post->getFirstMediaUrl('featured_image', 'thumb'); // Thumbnail (368x232)
$post->getFirstMediaUrl('featured_image', 'featured_image'); // Responsive
```

**In Blade templates**:

```blade
@if($post->hasMedia('featured_image'))
    <img src="{{ $post->getFirstMediaUrl('featured_image', 'thumb') }}"
         alt="{{ $post->title }}">
@endif
```

## Post Metadata

Store additional metadata on blog posts using the `meta` attribute:

```php
// Set metadata
$post->update([
    'meta' => [
        'description' => 'A brief description for social sharing',
        'reading_time' => '8 min read',
        'subtitle' => 'An optional subtitle',
    ],
]);

// Retrieve metadata
$post->getMeta('description', 'Default value');
```

## Working with Tags

Add tags to organize and categorize posts:

```php
// Attach tags
$post->attachTags(['laravel', 'php', 'tutorial']);

// Sync tags
$post->syncTags(['laravel', 'filament']);

// In Blade
@foreach($post->tags as $tag)
    <a href="/tags/{{ $tag->slug }}">{{ $tag->name }}</a>
@endforeach
```

**Learn more**: [Tags Documentation](/features/tags)

## Querying Blog Posts

Retrieve blog posts programmatically:

```php
use Siteman\Cms\Models\Page;

// Get all published posts from a blog
$posts = Page::where('parent_id', $blogId)
    ->published()
    ->get();

// Get posts with a specific tag
$posts = Page::withAnyTags(['laravel', 'php'])
    ->published()
    ->get();

// Get recent posts
$recentPosts = Page::where('parent_id', $blogId)
    ->published()
    ->take(5)
    ->get();

// Get posts by author
$authorPosts = Page::where('author_id', $authorId)
    ->published()
    ->get();
```

## Pagination

Customize pagination in your theme or controller:

```php
// In BlogIndex page type
$posts = $page->children()->published()->paginate(15); // 15 per page

// In your template
{{ $posts->links() }} // Laravel pagination links
```

**Custom pagination view**:

```blade
{{ $posts->links('my-theme::pagination') }}
```

## Multiple Blogs

Create multiple blog sections with different purposes:

1. **News Blog** (`/news`) - Company announcements
2. **Technical Blog** (`/blog`) - Technical tutorials
3. **Updates Blog** (`/updates`) - Product updates

Each can have its own:
- Custom template
- Tag taxonomy
- Author restrictions
- RSS feed

## RSS Feeds

Generate RSS feeds for your blog:

**Learn more**: [RSS Feed Documentation](/features/rss)

Quick example:

1. Create a page with type "RSS Feed"
2. Slug: `/blog/feed`
3. Configure feed settings

## SEO Optimization

Siteman includes SEO support for blog posts:

```php
// SEO data is automatically generated from:
// - Page title
// - Meta description
// - Author name
// - Published date
// - Featured image (for og:image)
```

**Customize SEO per post**:

```php
$post->update([
    'meta' => [
        'description' => 'Custom meta description for SEO',
        'og_title' => 'Custom Open Graph title',
        'og_description' => 'Custom OG description',
    ],
]);
```

## Best Practices

1. **Consistent Publishing**: Use the `published_at` field to schedule posts
2. **Featured Images**: Add featured images for better visual appeal
3. **Tags**: Use tags consistently for better organization
4. **Excerpts**: Store custom excerpts in `meta.description` for previews
5. **Hierarchical Structure**: Keep posts organized under appropriate blog indexes
6. **Versioning**: Siteman tracks versions of your posts automatically

## Related Features

- [Tags](/features/tags) - Organize posts with tags
- [Page Types](/features/page-types) - Understanding different page types
- [Themes](/features/themes) - Customize blog appearance
- [RSS Feeds](/features/rss) - Syndicate your blog content
