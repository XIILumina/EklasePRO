<?php

namespace Core\Middleware;

class Auth
{
    /**
     * @return void
     */
    public function handle(): void
    {
        if (!isset($_SESSION['auth'])) {
            header('Location: /login');
            die();
        }
    }
}