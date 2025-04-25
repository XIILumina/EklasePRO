<?php

namespace Core\Middleware;

class Teacher {
	public function handle(): void
	{
		if ($_SESSION['user']['role'] == 'teacher') {
			header('Location: /');
			die();
		}
	}
}