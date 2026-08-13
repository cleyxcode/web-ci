<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\PanelController;
use App\Models\UserModel;

class ProfilController extends PanelController
{
    public function index()
    {
        $user = model(UserModel::class)->find(current_user()['id']);

        return $this->render('mahasiswa/profil', [
            'title' => 'Profil Saya',
            'profil'=> $user,
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

        return redirect()->to('/mahasiswa/profil')->with('success', 'Profil diperbarui.');
    }

    public function changePassword()
    {
        $user = model(UserModel::class)->find(current_user()['id']);
        $current = $this->request->getPost('current_password');
        $new     = $this->request->getPost('new_password');

        if (! password_verify($current, $user['password'])) {
            return redirect()->back()->with('error', 'Password lama salah.');
        }

        model(UserModel::class)->update($user['id'], [
            'password' => password_hash($new, PASSWORD_BCRYPT),
        ]);

        return redirect()->to('/mahasiswa/profil')->with('success', 'Password berhasil diubah.');
    }
}
