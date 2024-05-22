<?php

namespace App\Model;

use App\Core\Auth\MissingCredentialsError;
use App\Core\Auth\NotAuthorizedError;
use App\Core\Auth\UserNotFoundError;
use App\Core\Config;
use App\Core\Storage\Storable;
use Firebase\JWT\JWT;

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

    protected static function preCreate($data = [])
    {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        return $data;
    }

    public static function getByName(string $name)
    {
        return self::getByPropertyValue('name', $name, true);
    }

    public static function getByEmail(string $email)
    {
        return self::getByPropertyValue('email', $email, true);
    }

    public static function login($email, $password)
    {
        if (!$email || !$password) {
            throw new MissingCredentialsError('Missing Credentials!');
        }

        $user = User::getByEmail($email);

        if (!$user) {
            throw new UserNotFoundError("User $email not found!");
        }

        if (!password_verify($password, $user->password)) {
            throw new NotAuthorizedError("Wrong password for $email!");
        }

        $payload = [
            'name' => $user->name,
            'uid' => $user->id,
            'iss' => Config::get('domain'),
            'aud' => Config::get('domain'),
            'iat' => time(),
            'exp' => time() + 86400 // 24h valid duration
        ];

        return JWT::encode($payload, Config::get('secret'), 'HS256');
    }
}
