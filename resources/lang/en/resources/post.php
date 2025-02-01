<?php

return [
    'navigation-group' => 'Content',
    'navigation-icon' => 'heroicon-o-newspaper',
    'navigation-label' => 'Posts',
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
    ],
    'table' => [
        'columns' => [
            'id' => 'ID',
            'title' => 'Title',
            'slug' => 'Slug',
            'content' => 'Content',
            'author' => 'Author',
        ],
        'filters' => [
            'search' => 'Search…',
        ],
        'actions' => [
            'edit' => 'Edit',
        ],
        'bulk-actions' => [
            'delete' => 'Delete',
        ],
    ],
];
