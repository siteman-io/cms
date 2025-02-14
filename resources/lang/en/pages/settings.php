<?php

return [
    'navigation-group' => 'Admin',
    'navigation-icon' => 'heroicon-o-cog',
    'navigation-label' => 'Settings',
    'heading' => 'Settings',
    'subheading' => 'Configure your Siteman site',
    'notifications' => [
        'saved' => ':group Settings successfully saved!',
    ],
    'form' => [
        'submit' => 'Save',
    ],
    'groups' => [
        'general' => [
            'label' => 'General',
            'fields' => [
                'site_name' => [
                    'label' => 'Site name',
                    'helper-text' => 'The name of your site. This will be used for the title and other Open Graph tags',
                ],
                'description' => [
                    'label' => 'Description',
                    'helper-text' => 'The description of your site. This will be used for the description and other Open Graph tags',
                ],
            ],
        ],
        'blog' => [
            'label' => 'Blogging',
            'fields' => [
                'enabled' => [
                    'label' => 'Enable blogging',
                    'helper-text' => 'This checkbox toggles the whole blogging functionality in Siteman',
                ],
                'blog_index_route' => [
                    'label' => 'Blog Route Prefix',
                    'helper-text' => 'Configure the blog index route. This also serves as a post prefix',
                ],
                'tag_route_prefix' => [
                    'label' => 'Tag Route Prefix',
                    'helper-text' => 'Configure the tag route prefix.',
                ],
                'rss_enabled' => [
                    'label' => 'Enable RSS feed',
                    'helper-text' => 'This checkbox toggles the RSS feed functionality in Siteman',
                ],
                'rss_endpoint' => [
                    'label' => 'The endpoint of the RSS feed',
                    'helper-text' => 'Configure the RSS feed endpoint as you like. Defaults to /rss',
                ],
            ],
        ],
    ],
];
