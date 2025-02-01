<?php

return [

    'item' => [
        'custom_text' => 'Custom Text',
        'custom_link' => 'Custom Link',
    ],
    'custom_link' => 'Custom Link',
    'custom_text' => 'Custom Text',
    'open_in' => [
        'label' => 'Open in',
        'options' => [
            'self' => 'Same tab',
            'blank' => 'New tab',
            'parent' => 'Parent tab',
            'top' => 'Top tab',
        ],
    ],
    'notifications' => [
        'created' => [
            'title' => 'Link created',
        ],
        'locations' => [
            'title' => 'Menu locations updated',
        ],
    ],
    'panel' => [
        'empty' => [
            'heading' => 'No items found',
            'description' => 'There are no items in this menu.',
        ],
        'pagination' => [
            'previous' => 'Previous',
            'next' => 'Next',
        ],
    ],
    'form' => [
        'title' => 'Title',
        'url' => 'URL',
        'linkable_type' => 'Type',
        'linkable_id' => 'ID',
    ],
    'resource' => [
        'navigation-group' => 'Admin',
        'navigation-icon' => 'heroicon-o-users',
        'navigation-label' => 'Menus',
        'fields' => [
            'name' => [
                'label' => 'Name',
            ],
            'is_visible' => [
                'label' => 'Visible',
                'visible' => 'Visible',
                'hidden' => 'Hidden',
            ],
            'locations' => [
                'label' => 'Locations',
                'empty' => 'Empty',
            ],
            'items' => [
                'label' => 'Items',
            ],
        ],
        'actions' => [
            'add' => [
                'label' => 'Add to Menu',
            ],
            'locations' => [
                'label' => 'Locations',
                'heading' => 'Manage Locations',
                'description' => 'Choose which menu appears at each location.',
                'submit' => 'Update',
                'form' => [
                    'location' => [
                        'label' => 'Location',
                    ],
                    'menu' => [
                        'label' => 'Assigned Menu',
                    ],
                ],
                'empty' => [
                    'heading' => 'No locations registered',
                ],
            ],
        ],
    ],
];
