<?php

return [
    'navigation-group' => 'Content',
    'navigation-icon' => 'heroicon-o-book-open',
    'navigation-label' => 'Pages',
    'fields' => [
        'title' => [
            'label' => 'Title',
            'helper-text' => 'The title of your page. It will also be used to generate the HTML title tag.',
        ],
        'slug' => [
            'label' => 'Slug',
            'helper-text' => 'This attribute will be used to generate the URL of your page.',
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
            'helper-text' => 'The date when the page will be published.',
        ],
        'layout' => [
            'label' => 'Layout',
            'helper-text' => 'Choose the lout for the page',
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
