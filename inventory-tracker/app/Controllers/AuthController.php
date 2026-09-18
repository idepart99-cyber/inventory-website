<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Models\User;

class AuthController extends Controller
{
    public function login(): void
    {
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }
        $this->render('auth/login', ['title' => 'Sign In'], 'layouts/auth');
    }

    public function postLogin(): void
    {
        $this->validateCsrf();
        $email = (string)$this->request('email', '');
        $password = (string)$this->request('password', '');

        if (empty($email) || empty($password)) {
            $this->redirect('/login', 'Please provide both email and password.', 'danger');
        }

        if (Auth::attempt($email, $password)) {
            $this->redirect('/dashboard', 'Welcome back, ' . Auth::user()['name'] . '!', 'success');
        } else {
            $this->redirect('/login', 'Invalid email or password credentials.', 'danger');
        }
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/login', 'You have been signed out successfully.', 'info');
    }

    public function profile(): void
    {
        $this->requireAuth();
        $user = User::find(Auth::id());
        $this->render('auth/profile', [
            'title' => 'My Profile & Security',
            'user' => $user
        ]);
    }

    public function updateProfile(): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $userId = Auth::id();
        $currentUser = User::find($userId);

        $name = trim((string)$this->request('name', ''));
        $email = trim((string)$this->request('email', ''));
        $password = (string)$this->request('password', '');
        $confirmPassword = (string)$this->request('password_confirmation', '');

        if (empty($name) || empty($email)) {
            $this->redirect('/profile', 'Name and email are required.', 'danger');
        }

        $updateData = [
            'name' => $name,
            'email' => $email,
            'role' => $currentUser['role'],
            'department_id' => $currentUser['department_id'],
            'status' => $currentUser['status']
        ];

        if (!empty($password)) {
            if ($password !== $confirmPassword) {
                $this->redirect('/profile', 'Passwords do not match.', 'danger');
            }
            if (strlen($password) < 6) {
                $this->redirect('/profile', 'Password must be at least 6 characters.', 'danger');
            }
            $updateData['password'] = $password;
        }

        User::update($userId, $updateData);
        Session::set('auth_user', User::find($userId));
        $this->redirect('/profile', 'Profile updated successfully.', 'success');
    }
}
