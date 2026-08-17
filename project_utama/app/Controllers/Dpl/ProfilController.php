<?php

namespace App\Controllers\Dpl;

use App\Controllers\PanelController;
use App\Models\DplModel;
use App\Models\UserModel;

class ProfilController extends PanelController
{
    public function index()
    {
        $user = model(UserModel::class)->find(current_user()['id']);
        $dpl  = model(DplModel::class)->findByUserId((int) current_user()['id']);

        return $this->render('dpl/profil', [
            'title'  => 'Profil DPL',
            'profil' => $user,
            'dpl'    => $dpl ?? [],
        ]);
    }

    public function update()
    {
        $userId = (int) current_user()['id'];

        if (! $this->validate([
            'nama'  => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email|max_length[100]',
            'prodi' => 'permit_empty|max_length[100]',
            'no_hp' => 'permit_empty|max_length[20]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nama  = trim((string) $this->request->getPost('nama'));
        $email = trim((string) $this->request->getPost('email'));
        $userModel = model(UserModel::class);
        $existing = $userModel->where('email', $email)->where('id !=', $userId)->first();

        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'Email sudah digunakan oleh akun lain.');
        }

        $userModel->update($userId, ['nama' => $nama, 'email' => $email]);
        $dplModel = model(DplModel::class);
        $dpl = $dplModel->findByUserId($userId);

        if ($dpl) {
            $dplModel->update($dpl['id'], [
                'nama'  => $nama,
                'prodi'  => trim((string) $this->request->getPost('prodi')) ?: null,
                'no_hp'  => trim((string) $this->request->getPost('no_hp')) ?: null,
            ]);
        }

        session()->set(['nama' => $nama, 'email' => $email]);

        return redirect()->to('/dpl/profil')->with('success', 'Profil diperbarui.');
    }

    public function changePassword()
    {
        $user = model(UserModel::class)->find(current_user()['id']);
        $current = (string) $this->request->getPost('current_password');
        $new = (string) $this->request->getPost('new_password');
        $confirm = (string) $this->request->getPost('confirm_password');

        if (! $user || ! password_verify($current, $user['password'])) {
            return redirect()->back()->with('error', 'Password lama salah.');
        }

        if (strlen($new) < 6 || $new !== $confirm) {
            return redirect()->back()->with('error', 'Password baru minimal 6 karakter dan harus sama dengan konfirmasi.');
        }

        model(UserModel::class)->update($user['id'], [
            'password' => password_hash($new, PASSWORD_BCRYPT),
        ]);

        return redirect()->to('/dpl/profil')->with('success', 'Password berhasil diubah.');
    }
}
