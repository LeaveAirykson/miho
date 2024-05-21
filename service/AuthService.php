<?php

namespace App\Service;

use App\Core\Auth\MissingCredentialsError;
use App\Core\Auth\NotAuthorizedError;
use App\Core\Auth\UserNotFoundError;
use App\Core\Config;
use Firebase\JWT\JWT;

class AuthService
{

    public static function login($email, $password)
    {
        if (!$email || !$password) {
            throw new MissingCredentialsError('Missing Credentials!');
        }

        $user = UserService::getByEmail($email);

        if (!$user) {
            throw new UserNotFoundError("User $email not found!");
        }

        if (!password_verify($password, $user->password)) {
            throw new NotAuthorizedError("Wrong password for $email!");
        }

        $payload = [
            'name' => $user->name,
            'iss' => Config::get('name'),
            'aud' => Config::get('domain'),
            'iat' => time(),
            'exp' => time() + 86400 // 24h valid duration
        ];

        return JWT::encode($payload, Config::get('secret'), 'HS256');
    }
}
