<?php

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
        $this->dplModel   = model(DplModel::class);
        $this->userModel  = model(UserModel::class);
    }

    public function index()
    {
        return $this->render('admin/dpl/index', [
            'title' => 'Data DPL',
            'dpl'   => $this->dplModel->getAllWithUser(),
        ]);
    }

    public function create()
    {
        return $this->render('admin/dpl/form', ['title' => 'Tambah DPL']);
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

        $userId = $this->userModel->insert([
            'nama'     => $this->request->getPost('nama'),
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'role'     => 'dpl',
        ]);

        $this->dplModel->insert([
            'user_id' => $userId,
            'nidn'    => $this->request->getPost('nidn'),
            'nama'    => $this->request->getPost('nama'),
            'prodi'   => $this->request->getPost('prodi'),
            'no_hp'   => $this->request->getPost('no_hp'),
        ]);

        return redirect()->to('/admin/dpl')->with('success', 'DPL berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $dpl = $this->dplModel->getWithUser($id);

        if (! $dpl) {
            return redirect()->to('/admin/dpl')->with('error', 'Data tidak ditemukan.');
        }

        return $this->render('admin/dpl/form', ['title' => 'Edit DPL', 'dpl' => $dpl]);
    }

    public function update(int $id)
    {
        $dpl = $this->dplModel->find($id);

        if (! $dpl) {
            return redirect()->to('/admin/dpl')->with('error', 'Data tidak ditemukan.');
        }

        $this->userModel->update($dpl['user_id'], [
            'nama'  => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
        ]);

        $this->dplModel->update($id, [
            'nidn'  => $this->request->getPost('nidn'),
            'nama'  => $this->request->getPost('nama'),
            'prodi' => $this->request->getPost('prodi'),
            'no_hp' => $this->request->getPost('no_hp'),
        ]);

        return redirect()->to('/admin/dpl')->with('success', 'Data DPL diperbarui.');
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
