<?php

return [
    'label' => 'Page',
    'plural-label' => 'Pages',
    'navigation' => [
        'group' => 'Content',
        'icon' => 'heroicon-o-book-open',
    ],
    'tree' => [
        'empty' => 'No pages found.',
        'items' => [
            'collapse' => 'Collapse',
            'expand' => 'Expand',
        ],
    ],
    'fields' => [
        'description' => [
            'label' => 'SEO description',
            'helper-text' => 'This value will be used for SEO purposes like meta description and OpenGraph tags.',
        ],
    ],
];
