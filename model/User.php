<?php

namespace App\Model;

use App\Core\Storage\Storable;

class User extends Storable
{
    static $fields = [
        'name' => [
            'constraints' => ['required']
        ],
        'email' => [
            'constraints' => ['required', 'email', 'unique'],
        ],
        'password' => [
            'constraints' => ['required'],
            'encryption' => 'password'
        ],
        'active' => [
            'default' => true,
        ]
    ];
}
