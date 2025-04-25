<?php

namespace App\Controllers;

use App\Models\User;

class PageController extends Controller {
	/**
	* @return void
	*/
	public function index(): void {
		view('index');
	}
}