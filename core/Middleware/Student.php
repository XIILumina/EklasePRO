<?php

namespace Core\Middleware;

class Student {
	public function handle(): void
	{
		if ($_SESSION['user']['role'] == 'student') {
			header('Location: /');
			die();
		}
	}
}