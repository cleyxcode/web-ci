<?php

namespace App\Controllers\Admin;

use App\Controllers\PanelController;
use App\Models\KelompokKknModel;
use App\Models\MahasiswaModel;
use App\Models\UserModel;

class MahasiswaController extends PanelController
{
    protected MahasiswaModel $mahasiswaModel;
    protected UserModel $userModel;
    protected KelompokKknModel $kelompokModel;

    public function __construct()
    {
        $this->mahasiswaModel = model(MahasiswaModel::class);
        $this->userModel      = model(UserModel::class);
        $this->kelompokModel  = model(KelompokKknModel::class);
    }

    public function index()
    {
        return $this->render('admin/mahasiswa/index', [
            'title'      => 'Data Mahasiswa',
            'mahasiswa'  => $this->mahasiswaModel->getAllWithRelations(),
            'kelompok'   => $this->kelompokModel->findAll(),
        ]);
    }

    public function create()
    {
        return $this->render('admin/mahasiswa/form', [
            'title'    => 'Tambah Mahasiswa',
            'kelompok' => $this->kelompokModel->findAll(),
        ]);
    }

    public function store()
    {
        $rules = [
            'nama'             => 'required|min_length[3]',
            'username'         => 'required|is_unique[users.username]',
            'email'            => 'required|valid_email|is_unique[users.email]',
            'npm'              => 'required|is_unique[mahasiswa.npm]',
            'password'         => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userId = $this->userModel->insert([
            'nama'     => $this->request->getPost('nama'),
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'role'     => 'mahasiswa',
        ]);

        $this->mahasiswaModel->insert([
            'user_id'    => $userId,
            'npm'        => $this->request->getPost('npm'),
            'nama'       => $this->request->getPost('nama'),
            'prodi'      => $this->request->getPost('prodi'),
            'kelompok_id'=> $this->request->getPost('kelompok_id') ?: null,
            'no_hp'      => $this->request->getPost('no_hp'),
        ]);

        return redirect()->to('/admin/mahasiswa')->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $mhs = $this->mahasiswaModel->getWithRelations($id);

        if (! $mhs) {
            return redirect()->to('/admin/mahasiswa')->with('error', 'Data tidak ditemukan.');
        }

        return $this->render('admin/mahasiswa/form', [
            'title'     => 'Edit Mahasiswa',
            'mahasiswa' => $mhs,
            'kelompok'  => $this->kelompokModel->findAll(),
        ]);
    }

    public function update(int $id)
    {
        $mhs = $this->mahasiswaModel->find($id);

        if (! $mhs) {
            return redirect()->to('/admin/mahasiswa')->with('error', 'Data tidak ditemukan.');
        }

        $rules = [
            'nama'     => 'required|min_length[3]',
            'email'    => "required|valid_email|is_unique[users.email,id,{$mhs['user_id']}]",
            'username' => "required|is_unique[users.username,id,{$mhs['user_id']}]",
            'npm'      => "required|is_unique[mahasiswa.npm,id,{$id}]",
        ];

        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[6]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userData = [
            'nama'     => $this->request->getPost('nama'),
            'email'    => $this->request->getPost('email'),
            'username' => $this->request->getPost('username'),
        ];

        if ($this->request->getPost('password')) {
            $userData['password'] = password_hash((string) $this->request->getPost('password'), PASSWORD_BCRYPT);
        }

        $this->userModel->update($mhs['user_id'], $userData);

        $this->mahasiswaModel->update($id, [
            'npm'         => $this->request->getPost('npm'),
            'nama'        => $this->request->getPost('nama'),
            'prodi'       => $this->request->getPost('prodi'),
            'kelompok_id' => $this->request->getPost('kelompok_id') ?: null,
            'no_hp'       => $this->request->getPost('no_hp'),
        ]);

        return redirect()->to('/admin/mahasiswa')->with('success', 'Data mahasiswa diperbarui.');
    }

    public function delete(int $id)
    {
        $mhs = $this->mahasiswaModel->find($id);

        if (! $mhs) {
            return redirect()->to('/admin/mahasiswa')->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        $db = db_connect();
        $db->transStart();
        $db->table('penilaian')->where('mahasiswa_id', $id)->delete();
        $db->table('evaluasi')->where('mahasiswa_id', $id)->delete();
        $this->userModel->delete((int) $mhs['user_id']);
        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->to('/admin/mahasiswa')->with('error', 'Mahasiswa gagal dihapus karena masih dipakai data lain.');
        }

        return redirect()->to('/admin/mahasiswa')->with('success', 'Mahasiswa dihapus.');
    }
}
