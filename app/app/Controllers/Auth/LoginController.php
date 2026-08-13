<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;

class LoginController extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = model(UserModel::class);
        helper(['form', 'url', 'app']);
    }

    public function index()
    {
        if (session()->get('logged_in')) {
            return redirect()->to($this->dashboardUrl(session()->get('role')));
        }

        return view('auth/login', ['title' => 'Login']);
    }

    public function authenticate()
    {
        $rules = [
            'login'    => 'required',
            'password' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $login    = trim((string) $this->request->getPost('login'));
        $password = (string) $this->request->getPost('password');
        $user     = $this->userModel->findByLogin($login);

        if (! $user || ! password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Username/email atau password salah.');
        }

        session()->set([
            'logged_in' => true,
            'user_id'   => $user['id'],
            'nama'      => $user['nama'],
            'username'  => $user['username'],
            'email'     => $user['email'],
            'role'      => $user['role'],
            'foto'      => $user['foto'],
        ]);

        return redirect()->to($this->dashboardUrl($user['role']))->with('success', 'Selamat datang, ' . $user['nama']);
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to('/login')->with('success', 'Anda telah logout.');
    }

    private function dashboardUrl(string $role): string
    {
        return match ($role) {
            'admin'     => '/admin/dashboard',
            'dpl'       => '/dpl/dashboard',
            'mahasiswa' => '/mahasiswa/dashboard',
            default     => '/login',
        };
    }
}
