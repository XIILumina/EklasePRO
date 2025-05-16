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
            'username' => 'required',
            'email' => 'required|email|unique:user,email',
            'password' => 'required|min:6',
        ]);

        $username = request('username');
        $email = request('email');
        $password = hash_make(request('password'));
        $first_name = request('first_name');
        $last_name = request('last_name');
        $role = 'student';

        try {
            User::create(compact('username', 'email', 'password', 'role'));
        } catch (\Exception $e) {
            Session::flash('error', 'Registration failed. Please try again.');
            redirect('/register');
        }

        $user = User::where('email', '=', $email)->get();

        if (!$user) {
            Session::flash('error', 'Registration failed. Please try again.');
            redirect('/register');
        }

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
            $file->path = 'users/';
            $file->randomFileName = true;
            $file->move();

            if (!$file->success()) {
                $errors = $file->displayUploadErrors();
                Session::flash('error', 'Failed to upload image: ' . implode(', ', $errors));
                redirect('/profile');
            }

            $image = 'storage/' . $file->path . $file->newFileName . $file->extension;

        } else if (request('image_url')) {
            $request->validate([
                'image_url' => 'required|url'
            ]);

            $image = request('image_url');
        } else {
            redirect('/profile');
        }

        $email = $_SESSION['user']['email'];
        $userData = User::where('email', '=', $email)->get();

        if (!$userData || !isset($userData['id'])) {
            Session::flash('error', 'User not found. Please log in again.');
            redirect('/login');
        }

        $id = (int) $userData['id'];

        if (isset($_SESSION['user']['image']) && $_SESSION['user']['image'] !== 'images/user.png') {
            $oldImagePath = __DIR__ . '/../../../public/' . $_SESSION['user']['image'];
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        User::update($id, compact('image'));
        $_SESSION['user']['image'] = $image;

        redirect('/profile');
    }

    public function update(Request $request): void
    {
        if (!isset($_SESSION['user'])) {
            redirect('/');
        }

        $request->validate([
            'username' => 'required',
            'email' => 'required|email'
        ]);

        $email = $_SESSION['user']['email'];
        $user = User::where('email', '=', $email)->get();

        if (!$user || !isset($user['id'])) {
            Session::flash('error', 'User not found. Please log in again.');
            redirect('/login');
        }

        $id = (int) $user['id'];
        $newUsername = request('username');
        $newEmail = request('email');

        User::update($id, [
            'username' => $newUsername,
            'email' => $newEmail
        ]);

        $_SESSION['user']['username'] = $newUsername;
        $_SESSION['user']['email'] = $newEmail;

        Session::flash('success', 'Profile updated successfully.');
        redirect('/profile');
    }

    public function delete(): void
    {
        if (!isset($_SESSION['user'])) {
            redirect('/');
        }

        $email = $_SESSION['user']['email'];
        $user = User::where('email', '=', $email)->get();

        if (!$user || !isset($user['id'])) {
            Session::flash('error', 'User not found. Please log in again.');
            redirect('/login');
        }

        $id = (int) $user['id'];
        User::delete($id);

        session_destroy();
        redirect('/');
    }

    public function deleteImage(): void
    {
        if (!isset($_SESSION['user'])) {
            redirect('/');
        }

        $email = $_SESSION['user']['email'];
        $user = User::where('email', '=', $email)->get();

        if (!$user || !isset($user['id'])) {
            Session::flash('error', 'User not found. Please log in again.');
            redirect('/login');
        }

        $id = (int) $user['id'];
        $currentImage = $_SESSION['user']['image'] ?? null;

        if ($currentImage && $currentImage !== 'images/user.png') {
            $imagePath = __DIR__ . '/../../../public/' . $currentImage;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        User::update($id, ['image' => null]);
        $_SESSION['user']['image'] = null;

        Session::flash('success', 'Avatar deleted successfully.');
        redirect('/profile');
    }
}