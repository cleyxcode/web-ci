<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\PanelController;
use App\Models\LogbookModel;
use App\Models\MahasiswaModel;

class LogbookController extends PanelController
{
    public function index()
    {
        $mhs = model(MahasiswaModel::class)->findByUserId(current_user()['id']);

        if (! $mhs) {
            return redirect()->to('/mahasiswa/dashboard')->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        return $this->render('mahasiswa/logbook/index', [
            'title'   => 'Logbook Kegiatan',
            'logbooks'=> model(LogbookModel::class)->getByMahasiswa($mhs['id']),
        ]);
    }

    public function create()
    {
        return $this->render('mahasiswa/logbook/form', ['title' => 'Tambah Logbook']);
    }

    public function edit(int $id)
    {
        $mhs = model(MahasiswaModel::class)->findByUserId(current_user()['id']);

        if (! $mhs) {
            return redirect()->to('/mahasiswa/dashboard');
        }

        $logbook = model(LogbookModel::class)->find($id);

        if (! $logbook || (int) $logbook['mahasiswa_id'] !== (int) $mhs['id']) {
            return redirect()->to('/mahasiswa/logbook')->with('error', 'Logbook tidak ditemukan.');
        }


        return $this->render('mahasiswa/logbook/form', [
            'title'   => 'Edit Logbook',
            'logbook' => $logbook,
        ]);
    }

    public function update(int $id)
    {
        $mhs = model(MahasiswaModel::class)->findByUserId(current_user()['id']);

        if (! $mhs) {
            return redirect()->to('/mahasiswa/dashboard');
        }

        $logbookModel = model(LogbookModel::class);
        $logbook = $logbookModel->find($id);

        if (! $logbook || (int) $logbook['mahasiswa_id'] !== (int) $mhs['id']) {
            return redirect()->to('/mahasiswa/logbook')->with('error', 'Logbook tidak ditemukan.');
        }


        if (! $this->validate([
            'tanggal'         => 'required|valid_date[Y-m-d]',
            'kegiatan'        => 'required|min_length[5]|max_length[5000]',
            'lokasi_kegiatan' => 'permit_empty|max_length[255]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $tanggal = (string) $this->request->getPost('tanggal');
        if (strtotime($tanggal) > strtotime(date('Y-m-d'))) {
            return redirect()->back()->withInput()->with('error', 'Tanggal kegiatan tidak boleh melebihi hari ini.');
        }

        $files = $this->request->getFileMultiple('dokumentasi') ?? [];
        if (count($files) > 3) return redirect()->back()->withInput()->with('error', 'Maksimal 3 gambar dokumentasi.');
        $dokumentasi = upload_files($files, 'logbook');

        $data = [
            'tanggal'         => $this->request->getPost('tanggal'),
            'kegiatan'        => trim((string) $this->request->getPost('kegiatan')),
            'lokasi_kegiatan' => trim((string) $this->request->getPost('lokasi_kegiatan')) ?: null,
        ];

        if ($dokumentasi !== []) {
            $data['dokumentasi'] = json_encode($dokumentasi, JSON_UNESCAPED_SLASHES);
        }

        $logbookModel->update($id, $data);

        return redirect()->to('/mahasiswa/logbook')->with('success', 'Logbook berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $mhs = model(MahasiswaModel::class)->findByUserId(current_user()['id']);

        if (! $mhs) {
            return redirect()->to('/mahasiswa/dashboard');
        }

        $logbookModel = model(LogbookModel::class);
        $logbook = $logbookModel->find($id);

        if (! $logbook || (int) $logbook['mahasiswa_id'] !== (int) $mhs['id']) {
            return redirect()->to('/mahasiswa/logbook')->with('error', 'Logbook tidak ditemukan.');
        }


        $logbookModel->delete($id);

        return redirect()->to('/mahasiswa/logbook')->with('success', 'Logbook berhasil dihapus.');
    }

    public function store()
    {
        $mhs = model(MahasiswaModel::class)->findByUserId(current_user()['id']);

        if (! $mhs) {
            return redirect()->to('/mahasiswa/dashboard');
        }

        if (! $this->validate([
            'tanggal'         => 'required|valid_date[Y-m-d]',
            'kegiatan'        => 'required|min_length[5]|max_length[5000]',
            'lokasi_kegiatan' => 'permit_empty|max_length[255]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $tanggal = (string) $this->request->getPost('tanggal');
        if (strtotime($tanggal) > strtotime(date('Y-m-d'))) {
            return redirect()->back()->withInput()->with('error', 'Tanggal kegiatan tidak boleh melebihi hari ini.');
        }

        $files = $this->request->getFileMultiple('dokumentasi') ?? [];
        if (count($files) > 3) return redirect()->back()->withInput()->with('error', 'Maksimal 3 gambar dokumentasi.');
        $dokumentasi = upload_files($files, 'logbook');

        model(LogbookModel::class)->insert([
            'mahasiswa_id'    => $mhs['id'],
            'tanggal'         => $this->request->getPost('tanggal'),
            'kegiatan'        => trim((string) $this->request->getPost('kegiatan')),
            'lokasi_kegiatan' => trim((string) $this->request->getPost('lokasi_kegiatan')) ?: null,
            'dokumentasi'     => $dokumentasi !== [] ? json_encode($dokumentasi, JSON_UNESCAPED_SLASHES) : null,
            'status'          => 'menunggu',
        ]);

        $this->pusher->trigger('kkn-channel', 'logbook.submitted', [
            'nama_mahasiswa' => $mhs['nama'],
            'npm'            => $mhs['npm'],
            'tanggal'        => $this->request->getPost('tanggal'),
        ]);

        $this->notifyDplOfMahasiswa(
            $mhs,
            'Logbook baru menunggu validasi',
            $mhs['nama'] . ' (' . $mhs['npm'] . ') mengirim logbook tanggal ' . format_tanggal($this->request->getPost('tanggal')),
            'warning'
        );
        $this->notifyAdmins(
            'Logbook baru masuk',
            $mhs['nama'] . ' (' . $mhs['npm'] . ') mengirim logbook kegiatan.',
            'info'
        );

        return redirect()->to('/mahasiswa/logbook')->with('success', 'Logbook berhasil disubmit.');
    }
}
