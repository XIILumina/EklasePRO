<?php

namespace Core\Middleware;

class Admin {
	public function handle(): void
	{
		if ($_SESSION['user']['role'] == 'admin') {
			header('Location: /');
			die();
		}
	}
}