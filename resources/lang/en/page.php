<?php

return [
    'label' => 'Page',
    'plural-label' => 'Pages',
    'navigation' => [
        'group' => 'Content',
        'icon' => 'heroicon-o-book-open',
    ],
    'notifications' => [
        'deleted' => [
            'title' => 'Moved to trash',
        ],
    ],
    'tree' => [
        'title' => 'Page Tree',
        'empty' => 'No pages found.',
        'empty_selection' => 'Please select a page from the tree to edit its content.',
        'items' => [
            'collapse' => 'Collapse',
            'expand' => 'Expand',
        ],
    ],
    'fields' => [
        'title' => [
            'label' => 'Title',
            'helper-text' => 'The title of your page. It will also be used to generate the HTML title tag.',
        ],
        'slug' => [
            'label' => 'Slug',
            'helper-text' => 'This attribute will be used to generate the URL of your post.',
        ],
        'content' => [
            'label' => 'Content',
            'helper-text' => 'The content in Markdown format.',
        ],
        'blocks' => [
            'label' => 'Blocks',
            'helper-text' => 'use blocks to customise your content.',
        ],
        'published_at' => [
            'label' => 'Published at',
            'helper-text' => 'The date when the post will be published.',
        ],
        'parent_id' => [
            'label' => 'Parent Page',
            'helper-text' => 'Select a parent page to create a hierarchy. Leave empty for a root page.',
        ],
        'layout' => [
            'label' => 'Layout',
            'helper-text' => 'Choose the lout for the post',
        ],
        'excerpt' => [
            'label' => 'Excerpt',
            'helper-text' => 'A kicker of your blog post. It will be used on index pages and in Meta tags',
        ],
        'image' => [
            'label' => 'Featured Image',
            'helper-text' => 'An optional image which will be used as a featured image.',
        ],
        'tags' => [
            'label' => 'Tags',
            'helper-text' => 'Tags are an option to loosely categorise your blog posts',
        ],
        'description' => [
            'label' => 'SEO description',
            'helper-text' => 'This value will be used for SEO purposes like meta description and OpenGraph tags.',
        ],
    ],
    'table' => [
        'columns' => [
            'id' => 'ID',
            'title' => 'Title',
            'slug' => 'Slug',
            'content' => 'Content',
            'author' => 'Author',
            'published_at' => 'Published at',
        ],
        'filters' => [
            'search' => 'Search…',
            'published' => [
                'label' => 'Published',
            ],
            'author' => [
                'label' => 'Author',
            ],
        ],
        'actions' => [
            'edit' => 'Edit',
            'delete' => 'Move to trash',
        ],
    ],
];
