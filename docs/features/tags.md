---
outline: deep
---

# Tags

Siteman CMS includes a robust tagging system powered by [Spatie's Laravel Tags package](https://github.com/spatie/laravel-tags). Tags help you organize and categorize content, making it easier for users to discover related pages and posts.

## Understanding Tags

Tags are labels or keywords attached to pages that:
- Group related content together
- Enable content discovery through tag-based navigation
- Create automatic taxonomy pages
- Improve SEO through topical clustering
- Provide metadata for filtering and searching

## Managing Tags in the Admin Panel

### Adding Tags to Pages

When creating or editing a page:

1. Navigate to **Content** > **Pages**
2. Create or edit a page
3. Find the **Tags** field in the page form
4. Start typing tag names:
   - Existing tags will appear as suggestions
   - Press Enter to create a new tag
   - Multiple tags can be added
5. Save the page

Tags are created automatically when you type them - no need to pre-create tags.

### Tag Auto-Complete

The tag field provides auto-complete functionality:
- Type to search existing tags
- See tag usage count
- Select from suggestions or create new tags

## Tag Index Pages

Create dedicated pages that display all content with a specific tag.

### Creating a Tag Index Page

1. Navigate to **Content** > **Pages**
2. Click **New Page**
3. Configure:
   - **Title**: "Tags" or "Topics"
   - **Slug**: "/tags"
   - **Type**: Select "Tag Index"
   - **Published At**: Current date/time
4. Click **Create**

The Tag Index will display all pages tagged with tags matching the URL pattern `/tags/{tag-slug}`.

### How Tag Index Works

When a user visits `/tags/laravel`, the Tag Index page automatically:
- Finds all published pages with the "laravel" tag
- Displays them in reverse chronological order
- Provides pagination
- Shows tag metadata

## Customizing Tag Views

### Tag Index Template

Create a custom view for tag index pages in your theme:

```
resources/views/siteman/themes/my-theme/tags/index.blade.php
```

**Example template**:

```blade
<x-my-theme::layout :page="$page">
    <div class="tag-index">
        <header>
            <h1>{{ $tag->name }}</h1>
            @if($tag->description)
                <p class="description">{{ $tag->description }}</p>
            @endif
            <p class="count">{{ $pages->total() }} {{ Str::plural('post', $pages->total()) }}</p>
        </header>

        <div class="tagged-content">
            @forelse($pages as $taggedPage)
                <article class="content-preview">
                    @if($taggedPage->hasMedia('featured_image'))
                        <img src="{{ $taggedPage->getFirstMediaUrl('featured_image', 'thumb') }}"
                             alt="{{ $taggedPage->title }}">
                    @endif

                    <h2>
                        <a href="{{ $taggedPage->computed_slug }}">
                            {{ $taggedPage->title }}
                        </a>
                    </h2>

                    <div class="meta">
                        <span class="date">{{ $taggedPage->published_at->format('F j, Y') }}</span>
                        <span class="author">By {{ $taggedPage->author->name }}</span>
                    </div>

                    <div class="excerpt">
                        {{ $taggedPage->getMeta('description', Str::limit($taggedPage->content, 150)) }}
                    </div>

                    <a href="{{ $taggedPage->computed_slug }}">Read more →</a>
                </article>
            @empty
                <p>No content found with this tag.</p>
            @endforelse
        </div>

        {{ $pages->links() }}

        <footer>
            <a href="/tags" class="back">← All tags</a>
        </footer>
    </div>
</x-my-theme::layout>
```

### All Tags Archive Page

Create a page listing all available tags:

```blade
{{-- resources/views/siteman/themes/my-theme/tags/archive.blade.php --}}

<x-my-theme::layout :page="$page">
    <div class="tags-archive">
        <h1>Browse by Topic</h1>

        <div class="tag-cloud">
            @foreach($tags as $tag)
                <a href="/tags/{{ $tag->slug }}"
                   class="tag"
                   style="font-size: {{ $tag->pages_count * 0.2 + 1 }}rem">
                    {{ $tag->name }}
                    <span class="count">({{ $tag->pages_count }})</span>
                </a>
            @endforeach
        </div>
    </div>
</x-my-theme::layout>
```

## Working with Tags Programmatically

### Attaching Tags

```php
use Siteman\Cms\Models\Page;

$page = Page::find(1);

// Attach single tag
$page->attachTag('laravel');

// Attach multiple tags
$page->attachTags(['laravel', 'php', 'tutorial']);

// Attach tag by ID
$page->attachTag(5);
```

### Syncing Tags

Replace all existing tags with a new set:

```php
// Remove all existing tags and add these
$page->syncTags(['laravel', 'filament']);

// Clear all tags
$page->syncTags([]);
```

### Detaching Tags

```php
// Remove single tag
$page->detachTag('outdated');

// Remove multiple tags
$page->detachTags(['draft', 'wip']);

// Remove all tags
$page->detachTags();
```

### Retrieving Tags

```php
// Get all tags for a page
$tags = $page->tags;

// Get tag names as array
$tagNames = $page->tags->pluck('name')->toArray();

// Check if page has a tag
if ($page->hasTag('laravel')) {
    // ...
}

// Check if page has any of these tags
if ($page->hasAnyTag(['laravel', 'php'])) {
    // ...
}

// Check if page has all these tags
if ($page->hasAllTags(['laravel', 'tutorial'])) {
    // ...
}
```

## Querying Pages by Tag

### Find Pages with Specific Tags

```php
use Siteman\Cms\Models\Page;

// Pages with any of these tags
$pages = Page::withAnyTags(['laravel', 'php'])
    ->published()
    ->get();

// Pages with all of these tags
$pages = Page::withAllTags(['laravel', 'tutorial'])
    ->published()
    ->get();

// Pages without these tags
$pages = Page::withoutTags(['draft', 'archived'])
    ->published()
    ->get();
```

### Get Tag with Pages Count

```php
use Spatie\Tags\Tag;

// Get all tags with page count
$tags = Tag::has('pages')
    ->withCount('pages')
    ->orderBy('pages_count', 'desc')
    ->get();

// Get popular tags (5 most used)
$popularTags = Tag::withCount('pages')
    ->orderBy('pages_count', 'desc')
    ->take(5)
    ->get();
```

## Tag Model

The `Tag` model includes these useful methods and attributes:

```php
use Spatie\Tags\Tag;

$tag = Tag::findFromString('laravel');

// Properties
$tag->name;        // Display name (e.g., "Laravel")
$tag->slug;        // URL-friendly slug (e.g., "laravel")
$tag->type;        // Tag type (default: null)
$tag->order_column; // Sort order

// Relationships
$tag->pages;       // All pages with this tag

// Methods
$tag->scopeContaining($query, $name); // Search tags
```

## Tag Types

Organize tags into different types (taxonomies):

```php
// Add tags with specific types
$page->attachTag('laravel', 'technology');
$page->attachTag('beginner', 'level');
$page->attachTag('2024', 'year');

// Query by tag type
$techTags = Tag::where('type', 'technology')->get();

// Get tags by type from page
$techTags = $page->tagsWithType('technology');
```

## Displaying Tags in Templates

### Tag List

```blade
{{-- Simple tag list --}}
<div class="tags">
    @foreach($page->tags as $tag)
        <a href="/tags/{{ $tag->slug }}" class="tag">
            {{ $tag->name }}
        </a>
    @endforeach
</div>
```

### Tag Cloud

```blade
{{-- Tag cloud with size based on usage --}}
@php
    $tags = \Spatie\Tags\Tag::withCount('pages')->get();
    $maxCount = $tags->max('pages_count');
@endphp

<div class="tag-cloud">
    @foreach($tags as $tag)
        @php
            $size = ($tag->pages_count / $maxCount) * 2 + 0.8; // Scale between 0.8 and 2.8rem
        @endphp
        <a href="/tags/{{ $tag->slug }}"
           class="tag"
           style="font-size: {{ $size }}rem">
            {{ $tag->name }}
        </a>
    @endforeach
</div>
```

### Related Posts by Tag

```blade
@if($page->tags->isNotEmpty())
    @php
        $relatedPages = \Siteman\Cms\Models\Page::withAnyTags($page->tags->pluck('name'))
            ->where('id', '!=', $page->id)
            ->published()
            ->take(3)
            ->get();
    @endphp

    @if($relatedPages->isNotEmpty())
        <aside class="related-posts">
            <h3>Related Articles</h3>
            <ul>
                @foreach($relatedPages as $relatedPage)
                    <li>
                        <a href="{{ $relatedPage->computed_slug }}">
                            {{ $relatedPage->title }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </aside>
    @endif
@endif
```

## Tag Slug Generation

Tags automatically generate URL-friendly slugs:

```php
$page->attachTag('Laravel & PHP'); // Creates slug: "laravel-php"
$page->attachTag('Web Development'); // Creates slug: "web-development"
```

Slugs are:
- Lowercase
- Spaces replaced with hyphens
- Special characters removed
- Unique per tag

## Tag Management Best Practices

1. **Consistent Naming**: Use consistent capitalization (e.g., "Laravel" not "laravel")
2. **Avoid Redundancy**: Don't use both "php" and "PHP"
3. **Specific over General**: "Laravel 11" is better than just "Laravel" for version-specific content
4. **Limit Tag Count**: Use 3-7 tags per page for optimal organization
5. **Create Tag Guidelines**: Document your tagging strategy for contributors
6. **Review Periodically**: Merge similar tags and remove unused ones

## Sitemap Integration

Include tagged pages in your sitemap:

```php
// In your sitemap controller
$tags = Tag::has('pages')->get();

foreach ($tags as $tag) {
    $sitemap->add(
        url("/tags/{$tag->slug}"),
        $tag->updated_at,
        '0.6',
        'weekly'
    );
}
```

## SEO Considerations

- **Tag Meta Descriptions**: Add descriptions to tag pages for better SEO
- **Canonical URLs**: Ensure tag pages have canonical URLs
- **No-Index**: Consider no-indexing tags with very few pages
- **Structured Data**: Add structured data for better search visibility

## Advanced Tag Features

### Custom Tag Attributes

Extend the Tag model to add custom attributes:

```php
// Migration
Schema::table('tags', function (Blueprint $table) {
    $table->string('description')->nullable();
    $table->string('color')->nullable();
    $table->string('icon')->nullable();
});

// Usage
$tag = Tag::findFromString('laravel');
$tag->update([
    'description' => 'All about Laravel framework',
    'color' => '#FF2D20',
    'icon' => 'laravel-icon.svg',
]);
```

### Tag Translations

For multi-language sites, use [Spatie's Laravel Tags Translation](https://spatie.be/docs/laravel-tags) features.

## Related Features

- [Blog](/features/blog) - Organize blog posts with tags
- [Page Types](/features/page-types) - Understanding Tag Index page type
- [Themes](/features/themes) - Customize tag page appearance
