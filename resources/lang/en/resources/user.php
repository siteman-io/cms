<?php

return [
    'navigation-group' => 'Admin',
    'navigation-icon' => 'heroicon-o-users',
    'navigation-label' => 'Users',
    'fields' => [
        'name' => [
            'label' => 'Name',
            'helper-text' => 'Enter your name',
        ],
        'email' => [
            'label' => 'Email',
            'helper-text' => 'Enter your email',
        ],
    ],
    'table' => [
        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'email' => 'Email',
            'roles' => 'Roles',
            'created_at' => 'Created at',
        ],
        'filters' => [
            'search' => 'Search…',
            'role' => [
                'label' => 'Role',
            ],
        ],
        'actions' => [
            'edit' => 'Edit',
        ],
        'bulk-actions' => [
            'delete' => 'Delete',
        ],
    ],
];
