<?php

namespace App\Controllers\Admin;

use App\Controllers\PanelController;
use App\Models\UserModel;

class ProfilController extends PanelController
{
    public function index()
    {
        $user = model(UserModel::class)->find(current_user()['id']);

        return $this->render('admin/profil', [
            'title'  => 'Profil Admin',
            'profil' => $user,
        ]);
    }

    public function update()
    {
        $userId = current_user()['id'];

        model(UserModel::class)->update($userId, [
            'nama'  => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
        ]);

        session()->set('nama', $this->request->getPost('nama'));
        session()->set('email', $this->request->getPost('email'));

        return redirect()->to('/admin/profil')->with('success', 'Profil diperbarui.');
    }

    public function changePassword()
    {
        $user    = model(UserModel::class)->find(current_user()['id']);
        $current = $this->request->getPost('current_password');
        $new     = $this->request->getPost('new_password');
        $confirm = $this->request->getPost('confirm_password');

        if (! password_verify($current, $user['password'])) {
            return redirect()->back()->with('error', 'Password lama salah.');
        }

        if ($new !== $confirm) {
            return redirect()->back()->with('error', 'Konfirmasi password tidak cocok.');
        }

        if (strlen($new) < 6) {
            return redirect()->back()->with('error', 'Password baru minimal 6 karakter.');
        }

        model(UserModel::class)->update($user['id'], [
            'password' => password_hash($new, PASSWORD_BCRYPT),
        ]);

        return redirect()->to('/admin/profil')->with('success', 'Password berhasil diubah.');
    }
}
