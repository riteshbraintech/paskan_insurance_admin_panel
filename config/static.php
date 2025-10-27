<?php

return [

    /*
    | Maintain all static key here
    |
    */

    'uplaodPath' => [
        'profile_image_path' => public_path('admin/upload/profile'),
        'productPath' => public_path('admin/upload/ecom/product'),
        'staffPath' => public_path('admin/upload/staff'),
        'strategyPath' => public_path('admin/upload/strategy'),
    ],
    'firebase' => [
        'api-key' => 'firebase-api-key',
        'sender-id' => 'firebase-sender-id',
        'server-key' => 'firebase-server-key',
    ],
    'permission' => [
        'role' => 'role-access',
        'staff' => 'staff-account',
        'member' => 'member-account',
        'strategy' => 'strategy-account',
        'transaction' => 'transaction',
    ],
    'permissionOpt' => [
        'read' => 'read',
        'create' => 'create',
        'update' => 'update',
        'delete' => 'delete',
    ]

];
