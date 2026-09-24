<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\PanelController;
use App\Models\LaporanModel;
use App\Models\MahasiswaModel;
use App\Models\KelompokKknModel;

class LaporanController extends PanelController
{
    private function getMahasiswaData()
    {
        $mhsBase = model(MahasiswaModel::class)->findByUserId(current_user()['id']);
        if (!$mhsBase) {
            return null;
        }
        $mhs = model(MahasiswaModel::class)->getWithRelations($mhsBase['id']);
        if ($mhs === null) return null;

        // Ambil ketua langsung dari kelompok agar hak upload tidak bergantung
        // pada hasil join profil mahasiswa.
        $kelompok = ! empty($mhs['kelompok_id'])
            ? model(KelompokKknModel::class)->find((int) $mhs['kelompok_id'])
            : null;
        $mhs['ketua_mahasiswa_id'] = $kelompok['ketua_mahasiswa_id'] ?? null;
        $mhs['is_ketua'] = $kelompok !== null
            && (int) ($kelompok['ketua_mahasiswa_id'] ?? 0) === (int) $mhs['id'];
        return $mhs;
    }

    public function index()
    {
        $mhs = $this->getMahasiswaData();
        if (! $mhs) return redirect()->to('/mahasiswa/dashboard');

        if (!empty($mhs['kelompok_id'])) {
            $laporan = model(LaporanModel::class)->getByKelompok($mhs['kelompok_id']);
        } else {
            $laporan = model(LaporanModel::class)->getByMahasiswa($mhs['id']);
        }

        return $this->render('mahasiswa/laporan/index', [
            'title'    => 'Laporan Kegiatan Kelompok',
            'laporan'  => $laporan,
            'is_ketua' => $mhs['is_ketua'],
        ]);
    }

    public function create()
    {
        $mhs = $this->getMahasiswaData();
        if (! $mhs) return redirect()->to('/mahasiswa/dashboard');

        if (!$mhs['is_ketua']) {
            return redirect()->to('/mahasiswa/laporan')->with('error', 'Hanya ketua kelompok yang dapat membuat laporan.');
        }

        return $this->render('mahasiswa/laporan/form', ['title' => 'Upload Laporan Kelompok']);
    }

    public function edit(int $id)
    {
        $mhs = $this->getMahasiswaData();
        if (! $mhs) return redirect()->to('/mahasiswa/dashboard');

        if (!$mhs['is_ketua']) {
            return redirect()->to('/mahasiswa/laporan')->with('error', 'Hanya ketua kelompok yang dapat mengedit laporan.');
        }

        $laporan = model(LaporanModel::class)->find($id);
        if (!$laporan) {
            return redirect()->to('/mahasiswa/laporan')->with('error', 'Laporan tidak ditemukan.');
        }

        $uploader = model(MahasiswaModel::class)->find($laporan['mahasiswa_id']);
        if (!$uploader || (int) $uploader['kelompok_id'] !== (int) $mhs['kelompok_id']) {
            return redirect()->to('/mahasiswa/laporan')->with('error', 'Laporan tidak ditemukan atau bukan milik kelompok Anda.');
        }

        if (($laporan['status'] ?? 'menunggu') !== 'menunggu') {
            return redirect()->to('/mahasiswa/laporan')->with('error', 'Laporan yang sudah diterima atau ditolak tidak dapat diedit.');
        }

        return $this->render('mahasiswa/laporan/form', [
            'title'   => 'Edit Laporan Kelompok',
            'laporan' => $laporan,
        ]);
    }

    public function update(int $id)
    {
        $mhs = $this->getMahasiswaData();
        if (! $mhs) return redirect()->to('/mahasiswa/dashboard');

        if (!$mhs['is_ketua']) {
            return redirect()->to('/mahasiswa/laporan')->with('error', 'Hanya ketua kelompok yang dapat mengedit laporan.');
        }

        $laporanModel = model(LaporanModel::class);
        $laporan = $laporanModel->find($id);
        if (!$laporan) {
            return redirect()->to('/mahasiswa/laporan')->with('error', 'Laporan tidak ditemukan.');
        }

        $uploader = model(MahasiswaModel::class)->find($laporan['mahasiswa_id']);
        if (!$uploader || (int) $uploader['kelompok_id'] !== (int) $mhs['kelompok_id']) {
            return redirect()->to('/mahasiswa/laporan')->with('error', 'Laporan tidak ditemukan atau bukan milik kelompok Anda.');
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

        return redirect()->to('/mahasiswa/laporan')->with('success', 'Laporan kelompok berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $mhs = $this->getMahasiswaData();
        if (! $mhs) return redirect()->to('/mahasiswa/dashboard');

        if (!$mhs['is_ketua']) {
            return redirect()->to('/mahasiswa/laporan')->with('error', 'Hanya ketua kelompok yang dapat menghapus laporan.');
        }

        $laporanModel = model(LaporanModel::class);
        $laporan = $laporanModel->find($id);
        if (!$laporan) {
            return redirect()->to('/mahasiswa/laporan')->with('error', 'Laporan tidak ditemukan.');
        }

        $uploader = model(MahasiswaModel::class)->find($laporan['mahasiswa_id']);
        if (!$uploader || (int) $uploader['kelompok_id'] !== (int) $mhs['kelompok_id']) {
            return redirect()->to('/mahasiswa/laporan')->with('error', 'Laporan tidak ditemukan atau bukan milik kelompok Anda.');
        }

        if (($laporan['status'] ?? 'menunggu') !== 'menunggu') {
            return redirect()->to('/mahasiswa/laporan')->with('error', 'Laporan yang sudah diterima atau ditolak tidak dapat dihapus.');
        }

        $laporanModel->delete($id);

        return redirect()->to('/mahasiswa/laporan')->with('success', 'Laporan kelompok berhasil dihapus.');
    }

    public function store()
    {
        $mhs = $this->getMahasiswaData();
        if (! $mhs) return redirect()->to('/mahasiswa/dashboard');

        if (!$mhs['is_ketua']) {
            return redirect()->to('/mahasiswa/laporan')->with('error', 'Hanya ketua kelompok yang dapat membuat laporan.');
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
            'mahasiswa_id' => $mhs['id'], // Stored under ketua's ID
            'judul'        => trim((string) $this->request->getPost('judul')),
            'deskripsi'    => trim((string) $this->request->getPost('deskripsi')) ?: null,
            'file_laporan' => $file,
            'status'       => 'menunggu',
        ]);

        $this->pusher->trigger('kkn-channel', 'laporan.submitted', [
            'nama_kelompok' => $mhs['nama_kelompok'] ?? $mhs['nama'],
            'judul'         => $this->request->getPost('judul'),
        ]);

        $this->notifyDplOfMahasiswa(
            $mhs,
            'Laporan kelompok baru menunggu review',
            'Kelompok ' . ($mhs['nama_kelompok'] ?? 'Unknown') . ' mengunggah laporan "' . $this->request->getPost('judul') . '".',
            'warning'
        );
        $this->notifyAdmins(
            'Laporan kelompok baru diunggah',
            'Kelompok ' . ($mhs['nama_kelompok'] ?? 'Unknown') . ' mengunggah laporan.',
            'info'
        );

        return redirect()->to('/mahasiswa/laporan')->with('success', 'Laporan kelompok berhasil diupload.');
    }
}
