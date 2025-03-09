---
outline: deep
---

# Page Types

Siteman uses pages of different page types to structure your website. A page type is an implementation of the
`Siteman\Cms\PageTypes\PageTypeInterface`.  
At the moment the following page types exist:

## `Page`

The `Page` page type is the default page type. It is used to create normal pages. Pages have a Block based editor to
create content.  
For more information on Blocks see [here](blocks.md).

## `Blog Index`

The `Blog Index` page type is used to create a blog index page. It lists all posts of the blog.

## `Tag Index`

The `Tag Index` page type is used to create a tag index page. It lists all tags of the blog.

## `RSS Feed`

The `RSS Feed` page type is used to create an RSS feed. It lists all posts of the blog in an RSS feed.
