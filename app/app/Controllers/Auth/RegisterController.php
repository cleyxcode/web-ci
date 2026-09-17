<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\MahasiswaModel;
use App\Models\UserModel;

class RegisterController extends BaseController
{
    protected UserModel $userModel;
    protected MahasiswaModel $mahasiswaModel;

    public function __construct()
    {
        $this->userModel      = model(UserModel::class);
        $this->mahasiswaModel = model(MahasiswaModel::class);
        helper(['form', 'url', 'app']);
    }

    // ── GET /register ─────────────────────────────────────────────────────────
    public function index(): string
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/mahasiswa/dashboard');
        }

        return view('auth/register', ['title' => 'Daftar Akun Mahasiswa']);
    }

    // ── POST /register ────────────────────────────────────────────────────────
    public function store()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/mahasiswa/dashboard');
        }

        $rules = [
            'nama'             => 'required|min_length[3]|max_length[100]',
            'email'            => 'required|valid_email|max_length[100]|is_unique[users.email]',
            'password'         => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
        ];

        $messages = [
            'nama'             => [
                'required'   => 'Nama lengkap wajib diisi.',
                'min_length' => 'Nama minimal 3 karakter.',
            ],
            'email'            => [
                'required'   => 'Email wajib diisi.',
                'valid_email' => 'Format email tidak valid.',
                'is_unique'  => 'Email sudah terdaftar. Silakan login atau gunakan email lain.',
            ],
            'password'         => [
                'required'   => 'Password wajib diisi.',
                'min_length' => 'Password minimal 6 karakter.',
            ],
            'password_confirm' => [
                'required' => 'Konfirmasi password wajib diisi.',
                'matches'  => 'Konfirmasi password tidak cocok.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = trim((string) $this->request->getPost('email'));
        $nama  = trim((string) $this->request->getPost('nama'));

        // Buat username otomatis dari bagian sebelum @ pada email
        $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', explode('@', $email)[0]));

        // Pastikan username unik
        $username = $baseUsername;
        $suffix   = 1;
        while ($this->userModel->where('username', $username)->countAllResults() > 0) {
            $username = $baseUsername . $suffix;
            $suffix++;
        }

        // Langsung insert user (tanpa OTP)
        $userId = $this->userModel->insert([
            'nama'      => $nama,
            'username'  => $username,
            'email'     => $email,
            'password'  => password_hash(
                (string) $this->request->getPost('password'),
                PASSWORD_BCRYPT
            ),
            'role'      => 'mahasiswa',
            'is_active' => 1,
        ]);

        // Insert baris mahasiswa (NPM temporary — akan diisi di profil)
        $this->mahasiswaModel->insert([
            'user_id'    => $userId,
            'npm'        => 'TEMP_' . $userId,  // temporary NPM unik
            'nama'       => $nama,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Auto-login
        $user = $this->userModel->find($userId);
        session()->set([
            'logged_in' => true,
            'user_id'   => $user['id'],
            'nama'      => $user['nama'],
            'username'  => $user['username'],
            'email'     => $user['email'],
            'role'      => $user['role'],
            'foto'      => $user['foto'],
        ]);

        // Notifikasi admin
        $admins = $this->userModel->where('role', 'admin')->findAll();
        $notif  = model(\App\Models\NotifikasiModel::class);
        foreach ($admins as $admin) {
            $notif->createNotif(
                (int) $admin['id'],
                'Mahasiswa baru mendaftar',
                $nama . ' (' . $email . ') baru mendaftar. NPM belum diisi.',
                'info'
            );
        }

        return redirect()->to('/mahasiswa/profil')
            ->with('success', 'Akun berhasil dibuat! Silakan lengkapi NPM dan data studi Anda.');
    }
}
