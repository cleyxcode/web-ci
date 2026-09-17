<?php

namespace App\Controllers\Admin;

use App\Controllers\PanelController;
use App\Models\PengumumanModel;
use App\Models\UserModel;

class PengumumanController extends PanelController
{
    protected PengumumanModel $pengumumanModel;

    public function __construct()
    {
        $this->pengumumanModel = model(PengumumanModel::class);
    }

    public function index()
    {
        return $this->render('admin/pengumuman/index', [
            'title'      => 'Pengumuman',
            'pengumuman' => $this->pengumumanModel->orderBy('created_at', 'DESC')->findAll(),
        ]);
    }

    public function create()
    {
        return $this->render('admin/pengumuman/form', ['title' => 'Buat Pengumuman']);
    }

    public function store()
    {
        if (! $this->validate([
            'judul' => 'required|min_length[3]|max_length[200]',
            'isi'   => 'required|min_length[3]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->pengumumanModel->insert([
            'judul'      => trim((string) $this->request->getPost('judul')),
            'isi'        => trim((string) $this->request->getPost('isi')),
            'created_by' => current_user()['id'],
        ]);

        $judul = (string) $this->request->getPost('judul');
        $isi   = mb_strimwidth((string) $this->request->getPost('isi'), 0, 120, '…');
        $users = model(UserModel::class)->where('is_active', 1)->findAll();

        foreach ($users as $u) {
            if ((int) $u['id'] === (int) current_user()['id']) {
                continue;
            }

            $this->notify((int) $u['id'], 'Pengumuman: ' . $judul, $isi, 'info');
        }

        return redirect()->to('/admin/pengumuman')->with('success', 'Pengumuman dipublikasikan.');
    }

    public function delete(int $id)
    {
        $this->pengumumanModel->delete($id);

        return redirect()->to('/admin/pengumuman')->with('success', 'Pengumuman dihapus.');
    }

    public function resetPassword()
    {
        if (! $this->validate([
            'user_id' => 'required|is_natural_no_zero',
            'password' => 'required|min_length[6]|max_length[72]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userId = (int) $this->request->getPost('user_id');
        $user   = model(UserModel::class)->find($userId);

        if (! $user) {
            return redirect()->back()->withInput()->with('error', 'User tidak ditemukan.');
        }

        $password = password_hash((string) $this->request->getPost('password'), PASSWORD_BCRYPT);

        model(UserModel::class)->update($userId, ['password' => $password]);

        return redirect()->back()->with('success', 'Password user berhasil direset.');
    }
}
