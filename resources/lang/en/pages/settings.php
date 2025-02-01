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
        'social' => [
            'label' => 'Social',
            'fields' => [
                'x_username' => [
                    'label' => 'X User name',
                    'helper-text' => 'Your X username. Keeping blank will also remove the X icon from the footer.',
                ],
                'github_username' => [
                    'label' => 'GitHub User name',
                    'helper-text' => 'Your GitHub username. Keeping blank will also remove the GitHub icon from the footer.',
                ],
                'linkedin_username' => [
                    'label' => 'LinkedIn User name',
                    'helper-text' => 'Your LinkedIn username. Keeping blank will also remove the LinkedIn icon from the footer.',
                ],
            ],
        ],
    ],
];
