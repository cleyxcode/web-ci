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

    public function store()
    {
        $mhs = model(MahasiswaModel::class)->findByUserId(current_user()['id']);

        if (! $mhs) {
            return redirect()->to('/mahasiswa/dashboard');
        }

        $rules = [
            'judul'        => 'required|max_length[200]',
            'deskripsi'    => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $file = upload_file($this->request->getFile('file_laporan'), 'laporan', ['pdf']);

        if (! $file) {
            return redirect()->back()->withInput()->with('error', 'File PDF wajib diupload (max 5MB).');
        }

        model(LaporanModel::class)->insert([
            'mahasiswa_id' => $mhs['id'],
            'judul'        => $this->request->getPost('judul'),
            'deskripsi'    => $this->request->getPost('deskripsi'),
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

    public function delete(int $id)
    {
        $mhs = model(MahasiswaModel::class)->findByUserId(current_user()['id']);

        if (! $mhs) {
            return redirect()->to('/mahasiswa/dashboard');
        }

        $laporanModel = model(LaporanModel::class);
        $laporan = $laporanModel->find($id);

        if (! $laporan || $laporan['mahasiswa_id'] != $mhs['id']) {
            return redirect()->to('/mahasiswa/laporan')->with('error', 'Laporan tidak ditemukan.');
        }

        // Logical rule: Can't delete if already 'diterima'
        if ($laporan['status'] === 'diterima') {
            return redirect()->to('/mahasiswa/laporan')->with('error', 'Laporan yang sudah diterima tidak dapat dihapus.');
        }

        if (!empty($laporan['file_laporan']) && file_exists(FCPATH . 'uploads/' . $laporan['file_laporan'])) {
            @unlink(FCPATH . 'uploads/' . $laporan['file_laporan']);
        }

        $laporanModel->delete($id);

        return redirect()->to('/mahasiswa/laporan')->with('success', 'Laporan berhasil dihapus.');
    }
}
