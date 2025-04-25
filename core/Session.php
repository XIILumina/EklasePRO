<?php

namespace Core;

class Session
{
    /**
     * @return void
     * @param mixed $key
     * @param mixed $value
     */
    public static function put($key, $value): void
    {
        $_SESSION[$key] = $value;
    }
    /**
     * @param mixed $key
     * @param mixed $default
     */
    public static function get($key, $default = null): mixed
    {
        return $_SESSION['_flash'][$key] ?? $_SESSION[$key] ?? $default;
    }
    /**
     * @return void
     * @param mixed $key
     * @param mixed $value
     */
    public static function flash($key, $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }
    /**
     * @return void
     */
    public static function unflash(): void
    {
        unset($_SESSION['_flash']);
    }
    /**
     * @return void
     */
    public static function flush(): void
    {
        $_SESSION = [];
    }
}