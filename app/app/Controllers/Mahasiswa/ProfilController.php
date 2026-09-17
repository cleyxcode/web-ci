<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\PanelController;
use App\Models\MahasiswaModel;
use App\Models\UserModel;

class ProfilController extends PanelController
{
    public function index()
    {
        $user      = model(UserModel::class)->find(current_user()['id']);
        $mhsModel  = model(MahasiswaModel::class);
        $mahasiswa = $mhsModel->findByUserId((int) current_user()['id']);
        $detail    = $mahasiswa ? $mhsModel->getWithRelations((int) $mahasiswa['id']) : [];

        return $this->render('mahasiswa/profil', [
            'title'     => 'Profil Saya',
            'profil'    => $user,
            'mahasiswa' => $detail ?: ($mahasiswa ?? []),
            'isKetua'   => ! empty($detail['kelompok_id'])
                && (int) ($detail['ketua_mahasiswa_id'] ?? 0) === (int) ($detail['id'] ?? 0),
        ]);
    }

    // ── Simpan nama & email ───────────────────────────────────────────────────
    public function update()
    {
        $userId = current_user()['id'];

        $rules = [
            'nama'  => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email|max_length[100]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nama  = trim((string) $this->request->getPost('nama'));
        $email = trim((string) $this->request->getPost('email'));

        // Cek email tidak dipakai user lain
        $existing = model(UserModel::class)
            ->where('email', $email)
            ->where('id !=', $userId)
            ->first();

        if ($existing) {
            return redirect()->back()->withInput()
                ->with('error', 'Email sudah digunakan oleh akun lain.');
        }

        model(UserModel::class)->update($userId, ['nama' => $nama, 'email' => $email]);

        // Update nama di tabel mahasiswa juga agar konsisten
        $mhsModel = model(MahasiswaModel::class);
        $mhs      = $mhsModel->findByUserId($userId);
        if ($mhs) {
            $mhsModel->update($mhs['id'], ['nama' => $nama]);
        }

        session()->set('nama', $nama);
        session()->set('email', $email);

        return redirect()->to('/mahasiswa/profil')->with('success', 'Profil akun diperbarui.');
    }

    // ── Simpan NPM, prodi, no_hp ──────────────────────────────────────────────
    public function updateData()
    {
        $userId = current_user()['id'];
        $npm    = trim((string) ($this->request->getPost('npm') ?? ''));
        $prodi  = trim((string) ($this->request->getPost('prodi') ?? ''));
        $noHp   = trim((string) ($this->request->getPost('no_hp') ?? ''));

        if ($npm === '') {
            return redirect()->back()->withInput()
                ->with('error', 'NPM wajib diisi.');
        }

        $mhsModel = model(MahasiswaModel::class);
        $mhs      = $mhsModel->findByUserId($userId);

        if (! $mhs) {
            // Seharusnya tidak terjadi, tapi jaga-jaga: buat baris mahasiswa
            $mhsModel->insert([
                'user_id' => $userId,
                'npm'     => $npm,
                'nama'    => current_user()['nama'],
                'prodi'   => $prodi ?: null,
                'no_hp'   => $noHp ?: null,
            ]);

            return redirect()->to('/mahasiswa/profil')
                ->with('success', 'Data studi berhasil disimpan.');
        }

        // Cek NPM unik (tidak boleh milik mahasiswa lain)
        if ($npm !== ($mhs['npm'] ?? '')) {
            $npmTaken = $mhsModel->where('npm', $npm)
                ->where('id !=', $mhs['id'])
                ->first();

            if ($npmTaken) {
                return redirect()->back()->withInput()
                    ->with('error', 'NPM sudah terdaftar untuk mahasiswa lain.');
            }
        }

        $mhsModel->update($mhs['id'], [
            'npm'   => $npm,
            'prodi' => $prodi ?: null,
            'no_hp' => $noHp ?: null,
        ]);

        return redirect()->to('/mahasiswa/profil')
            ->with('success', 'Data studi berhasil disimpan.');
    }

    // ── Ganti password ────────────────────────────────────────────────────────
    public function changePassword()
    {
        $user    = model(UserModel::class)->find(current_user()['id']);
        $current = (string) $this->request->getPost('current_password');
        $new     = (string) $this->request->getPost('new_password');

        if (! password_verify($current, $user['password'])) {
            return redirect()->back()->with('error', 'Password lama salah.');
        }

        if (strlen($new) < 6) {
            return redirect()->back()
                ->with('error', 'Password baru minimal 6 karakter.');
        }

        model(UserModel::class)->update($user['id'], [
            'password' => password_hash($new, PASSWORD_BCRYPT),
        ]);

        return redirect()->to('/mahasiswa/profil')
            ->with('success', 'Password berhasil diubah.');
    }
}
