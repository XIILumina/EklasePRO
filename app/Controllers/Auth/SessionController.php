<?php
namespace App\Controllers\Auth;

use App\Controllers\Controller;
use App\Models\User;
use Core\Request;
use Core\Router;
use Core\Session;

class SessionController extends Controller
{
    public function create(): mixed
    {
        if (isset($_SESSION['user'])) {
            redirect(Router::previousUrl());
        }

        return view('auth/login', ['title' => 'Login']);
    }
    /**
     * @return void
     */
    public function store(Request $request): void
    {
        if (isset($_SESSION['user'])) {
            redirect(Router::previousUrl());
        }

        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $email    = request('email');
        $password = request('password');

        $user = User::where('email', '=', $email)->get();

        if (! $user || ! password_verify($password, $user['password'])) {
            Session::flash('errors', ['password' => 'Invalid username or password']);
            Session::put('old', ['email' => $email]);
            redirect('/login');
        }

        unset($user['password']);

        $_SESSION['user'] = $user;

        redirect('/');
    }
    /**
     * @return void
     */
    public function destroy(): void
    {
        if (! isset($_SESSION['user'])) {
            header(Router::previousUrl());
            die();
        }

        unset($_SESSION['user']);

        header('Location: /');
    }
}