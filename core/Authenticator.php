<?php

namespace Core;

use App\Models\User;

class Authenticator
{
    /**
     * @return bool
     * @param  mixed $email
     * @param  mixed $password
     */
    public static function attempt(mixed $email, mixed $password): bool
    {
        $user = User::where('email', '=', $email)->get();

        if ($user) {
            if (password_verify($password, $user['password'])) {
                static::login($user);

                return true;
            }
        }

        return false;
    }

    /**
     * @return void
     * @param  mixed $user
     */
    public static function login(mixed $user): void
    {
        if ($user) {
            $_SESSION['user'] = $user;
        }

        session_regenerate_id(true);
    }

    /**
     * @return void
     */
    public static function logout(): void
    {
        Session::flush();
    }
}