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
            'constraints' => ['required']
        ],
        'active' => [
            'default' => true,
        ]
    ];

    function prepare()
    {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }
}
