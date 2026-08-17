<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\PanelController;
use App\Models\LaporanModel;
use App\Models\MahasiswaModel;

class LaporanController extends PanelController
{
    public function index()
    {
        $mhs = model(MahasiswaModel::class)->findByUserId(current_user()['id']);

        if (! $mhs) {
            return redirect()->to('/mahasiswa/dashboard');
        }

        return $this->render('mahasiswa/laporan/index', [
            'title'   => 'Laporan Kegiatan',
            'laporan' => model(LaporanModel::class)->getByMahasiswa($mhs['id']),
        ]);
    }

    public function create()
    {
        return $this->render('mahasiswa/laporan/form', ['title' => 'Upload Laporan']);
    }

    public function edit(int $id)
    {
        $mhs = model(MahasiswaModel::class)->findByUserId(current_user()['id']);

        if (! $mhs) {
            return redirect()->to('/mahasiswa/dashboard');
        }

        $laporan = model(LaporanModel::class)->find($id);

        if (! $laporan || (int) $laporan['mahasiswa_id'] !== (int) $mhs['id']) {
            return redirect()->to('/mahasiswa/laporan')->with('error', 'Laporan tidak ditemukan.');
        }

        if (($laporan['status'] ?? 'menunggu') !== 'menunggu') {
            return redirect()->to('/mahasiswa/laporan')->with('error', 'Laporan yang sudah diterima atau ditolak tidak dapat diedit.');
        }

        return $this->render('mahasiswa/laporan/form', [
            'title'   => 'Edit Laporan',
            'laporan' => $laporan,
        ]);
    }

    public function update(int $id)
    {
        $mhs = model(MahasiswaModel::class)->findByUserId(current_user()['id']);

        if (! $mhs) {
            return redirect()->to('/mahasiswa/dashboard');
        }

        $laporanModel = model(LaporanModel::class);
        $laporan = $laporanModel->find($id);

        if (! $laporan || (int) $laporan['mahasiswa_id'] !== (int) $mhs['id']) {
            return redirect()->to('/mahasiswa/laporan')->with('error', 'Laporan tidak ditemukan.');
        }

        if (($laporan['status'] ?? 'menunggu') !== 'menunggu') {
            return redirect()->to('/mahasiswa/laporan')->with('error', 'Laporan yang sudah diterima atau ditolak tidak dapat diedit.');
        }

        if (! $this->validate([
            'judul'     => 'required|min_length[5]|max_length[200]',
            'deskripsi' => 'permit_empty|max_length[5000]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $file = upload_file($this->request->getFile('file_laporan'), 'laporan', ['pdf']);

        $data = [
            'judul'     => trim((string) $this->request->getPost('judul')),
            'deskripsi' => trim((string) $this->request->getPost('deskripsi')) ?: null,
        ];

        if ($file) {
            $data['file_laporan'] = $file;
        }

        $laporanModel->update($id, $data);

        return redirect()->to('/mahasiswa/laporan')->with('success', 'Laporan berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $mhs = model(MahasiswaModel::class)->findByUserId(current_user()['id']);

        if (! $mhs) {
            return redirect()->to('/mahasiswa/dashboard');
        }

        $laporanModel = model(LaporanModel::class);
        $laporan = $laporanModel->find($id);

        if (! $laporan || (int) $laporan['mahasiswa_id'] !== (int) $mhs['id']) {
            return redirect()->to('/mahasiswa/laporan')->with('error', 'Laporan tidak ditemukan.');
        }

        if (($laporan['status'] ?? 'menunggu') !== 'menunggu') {
            return redirect()->to('/mahasiswa/laporan')->with('error', 'Laporan yang sudah diterima atau ditolak tidak dapat dihapus.');
        }

        $laporanModel->delete($id);

        return redirect()->to('/mahasiswa/laporan')->with('success', 'Laporan berhasil dihapus.');
    }

    public function store()
    {
        $mhs = model(MahasiswaModel::class)->findByUserId(current_user()['id']);

        if (! $mhs) {
            return redirect()->to('/mahasiswa/dashboard');
        }

        if (! $this->validate([
            'judul'     => 'required|min_length[5]|max_length[200]',
            'deskripsi' => 'permit_empty|max_length[5000]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $file = upload_file($this->request->getFile('file_laporan'), 'laporan', ['pdf']);

        if (! $file) {
            return redirect()->back()->withInput()->with('error', 'File PDF wajib diupload (max 5MB).');
        }

        model(LaporanModel::class)->insert([
            'mahasiswa_id' => $mhs['id'],
            'judul'        => trim((string) $this->request->getPost('judul')),
            'deskripsi'    => trim((string) $this->request->getPost('deskripsi')) ?: null,
            'file_laporan' => $file,
            'status'       => 'menunggu',
        ]);

        $this->pusher->trigger('kkn-channel', 'laporan.submitted', [
            'nama_mahasiswa' => $mhs['nama'],
            'judul'          => $this->request->getPost('judul'),
        ]);

        $this->notifyDplOfMahasiswa(
            $mhs,
            'Laporan baru menunggu review',
            $mhs['nama'] . ' mengunggah laporan "' . $this->request->getPost('judul') . '".',
            'warning'
        );
        $this->notifyAdmins(
            'Laporan baru diunggah',
            $mhs['nama'] . ' mengunggah laporan "' . $this->request->getPost('judul') . '".',
            'info'
        );

        return redirect()->to('/mahasiswa/laporan')->with('success', 'Laporan berhasil diupload.');
    }
}
