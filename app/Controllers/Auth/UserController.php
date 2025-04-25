<?php

namespace App\Controllers\Auth;

use App\Controllers\Controller;
use App\Models\User;
use Core\Authenticator;
use Core\FileUpload;
use Core\Request;
use Core\Validator;
use Core\Session;

class UserController extends Controller
{
    public function show(): void
    {
        if (!isset($_SESSION['user'])) {
            redirect('/');
        }

        view('auth/profile', ['title' => 'Profile']);
        return;
    }
    public function create(): void
    {
        if (isset($_SESSION['user'])) {
            redirect('/');
        }

        view('auth/register', ['title' => 'Register']);
        return;
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:user,email',
            'password' => 'required|min:6'
        ]);

        $name = request('name');
        $email = request('email');
        $password = hash_make(request('password'));
        $image = 'images/user.png';

        User::create(compact('name', 'email', 'password', 'image'));

        $user = User::where('email', '=', $email)->get();

        Authenticator::login($user);

        unset($user['password']);

        redirect_and_save('/', [], $user, 'User', 'store');
    }

    public function image(Request $request): void
    {
        if (request('image')['size'] !== 0) {

            $request->validate([
                'image' => 'required|image'
            ]);

            $file = new FileUpload('image');
            $file->path('/users/');
            $file->createRandomName();
            $file->upload();

            $image = 'storage/users/' . $file->newFileName . $file->extension;

        } else if (request('image_url')) {

            $request->validate([
                'image_url' => 'required|url'
            ]);

            $image = request('image_url');

        } else {
            redirect('/profile');
        }

        $email = $_SESSION['user']['email'];
        $id = User::where('email', '=' , $email)->get()['ID'];

        unlink($_SESSION['user']['image']);

        User::update($id, compact('image'));

        $_SESSION['user']['image'] = $image;

        redirect('/profile');
    }
}