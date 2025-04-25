<?php

namespace Core\Middleware;

class Guest
{
    /**
     * @return void
     */
    public function handle(): void
    {
        if ($_SESSION['auth'] ?? false) {
            header('Location: /');
            die();
        }
    }
}