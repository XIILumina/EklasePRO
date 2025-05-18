<?php

namespace Core;

function loadEnv($path): void
{
    if (!file_exists($path)) {
        error_log("Environment file not found: $path");
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        [$name, $value] = explode('=', $line, 2) + [1 => ''];
        $name = trim($name);
        $value = trim($value, " \t\n\r\0\x0B\"'");
        if (!empty($name)) {
            putenv("$name=$value");
            $_ENV[$name] = $value;
        }
    }
}

function env($key, $default = null)
{
    $value = getenv($key) ?: $_ENV[$key] ?? $default;
    return $value;
}

// Load .env file automatically
loadEnv(BASE_PATH . '.env');