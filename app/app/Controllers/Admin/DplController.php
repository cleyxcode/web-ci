<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\PanelController;
use App\Models\DplModel;
use App\Models\UserModel;

class DplController extends PanelController
{
    protected DplModel $dplModel;
    protected UserModel $userModel;

    public function __construct()
    {
        $this->dplModel  = model(DplModel::class);
        $this->userModel = model(UserModel::class);
    }

    public function index()
    {
        return $this->render('admin/dpl/index', [
            'title'       => 'Kelola Data DPL',
            'dpl'         => $this->dplModel->getAllWithUser(),
            'credentials' => session()->getFlashdata('dpl_credentials'),
        ]);
    }

    public function create()
    {
        return $this->render('admin/dpl/form', [
            'title' => 'Tambah Akun DPL',
        ]);
    }

    public function store()
    {
        $rules = [
            'nama'     => 'required',
            'username' => 'required|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'nidn'     => 'required',
            'password' => 'required|min_length[6]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');
        $nama     = trim((string) $this->request->getPost('nama'));

        $userId = $this->userModel->insert([
            'nama'      => $nama,
            'username'  => $username,
            'email'     => $this->request->getPost('email'),
            'password'  => password_hash($password, PASSWORD_BCRYPT),
            'role'      => 'dpl',
            'is_active' => 1,
        ]);

        $this->dplModel->insert([
            'user_id' => $userId,
            'nidn'    => $this->request->getPost('nidn'),
            'nama'    => $nama,
            'prodi'   => $this->request->getPost('prodi'),
            'no_hp'   => $this->request->getPost('no_hp'),
        ]);

        return redirect()->to('/admin/dpl')
            ->with('success', 'Akun DPL berhasil dibuat. Bagikan kredensial di bawah kepada dosen yang bersangkutan.')
            ->with('dpl_credentials', [
                'nama'     => $nama,
                'username' => $username,
                'password' => $password,
            ]);
    }

    public function edit(int $id)
    {
        $dpl = $this->dplModel->getWithUser($id);

        if (! $dpl) {
            return redirect()->to('/admin/dpl')->with('error', 'Data tidak ditemukan.');
        }

        return $this->render('admin/dpl/form', [
            'title' => 'Edit DPL',
            'dpl'   => $dpl,
        ]);
    }

    public function update(int $id)
    {
        $dpl = $this->dplModel->find($id);

        if (! $dpl) {
            return redirect()->to('/admin/dpl')->with('error', 'Data tidak ditemukan.');
        }

        $userData = [
            'nama'  => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
        ];

        $newPassword = trim((string) $this->request->getPost('password'));
        $credentials = null;

        if ($newPassword !== '') {
            if (strlen($newPassword) < 6) {
                return redirect()->back()->withInput()->with('error', 'Password baru minimal 6 karakter.');
            }

            $userData['password'] = password_hash($newPassword, PASSWORD_BCRYPT);
            $user                 = $this->userModel->find((int) $dpl['user_id']);
            $credentials          = [
                'nama'     => $this->request->getPost('nama'),
                'username' => $user['username'] ?? '',
                'password' => $newPassword,
            ];
        }

        $this->userModel->update((int) $dpl['user_id'], $userData);

        $this->dplModel->update($id, [
            'nidn'  => $this->request->getPost('nidn'),
            'nama'  => $this->request->getPost('nama'),
            'prodi' => $this->request->getPost('prodi'),
            'no_hp' => $this->request->getPost('no_hp'),
        ]);

        $redirect = redirect()->to('/admin/dpl')->with('success', 'Data DPL diperbarui.');

        if ($credentials !== null) {
            $redirect->with('dpl_credentials', $credentials)
                ->with('success', 'Data DPL diperbarui. Password baru siap dibagikan kepada DPL.');
        }

        return $redirect;
    }

    public function delete(int $id)
    {
        $dpl = $this->dplModel->find($id);

        if ($dpl) {
            $this->userModel->delete($dpl['user_id']);
        }

        return redirect()->to('/admin/dpl')->with('success', 'DPL dihapus.');
    }
}
