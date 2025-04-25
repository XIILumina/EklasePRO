<?php

namespace App\Controllers;

abstract class Controller
{
    /**
     * @param array<int,mixed> $errors
     */
    public function incorrectPayload(string $view, array $errors): mixed
	{
		return view($view, [
			'errors' => $errors
		]);
	}
}