<?php

namespace Core\Middleware;

use Exception;

class Middleware
{
    public const MAP = [
        'guest' => Guest::class,
        'auth' => Auth::class,
		'admin' => Admin::class,
		'student' => Student::class,
        'teacher' => Teacher::class,
    ];

    /**
     * @param mixed $key
     * @return void
     * @throws Exception
     */
    public static function resolve(mixed $key): void
    {
        if (!$key) {
            return;
        }

        $middleware = static::MAP[$key] ?? false;

        if (!$middleware) {
            throw new Exception("No matching middleware found for key '{$key}'.");
        }

        (new $middleware)->handle();
    }
}