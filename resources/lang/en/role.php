<?php

return [
    'label' => 'Role',
    'plural-label' => 'Roles',
    'navigation' => [
        'group' => 'Admin',
        'icon' => 'heroicon-o-shield-check',
    ],
    'table' => [
        'columns' => [
            'name' => 'Name',
            'guard_name' => 'Guard Name',
            'permissions_count' => '# of Permissions',
            'users_count' => '# of Users',
            'updated_at' => 'Updated at',
        ],
    ],
];
