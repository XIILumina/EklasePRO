<?php

namespace App\Controllers;

use App\Models\User;

class MailController extends Controller {
	/**
	* @return void
	*/
	public function index(): void {
		view('index');
	}


    /**
    * @return void
    */
    public function show(): void {
        view('diary.show');
    }



    /**
    * @return void
    */
    public function create(): void {
        view('diary.create');
    }


    /**
    * @return void
    */
    public function store(): void {
        // Logic to store diary entry
        redirect('/diary');
    }


    /**
    * @return void
    */
    public function edit($id): void {
        view('diary.edit', ['id' => $id]);
    }


    /**
    * @return void
    */
    public function update($id): void {
        // Logic to update diary entry
        redirect('/diary/' . $id);
    }


    /**
    * @return void
    */
    public function delete($id): void {
        // Logic to delete diary entry
        redirect('/diary');
    }
}