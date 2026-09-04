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

    public function store()
    {
        $mhs = model(MahasiswaModel::class)->findByUserId(current_user()['id']);

        if (! $mhs) {
            return redirect()->to('/mahasiswa/dashboard');
        }

        $rules = [
            'tanggal'         => 'required|valid_date',
            'kegiatan'        => 'required',
            'lokasi_kegiatan' => 'permit_empty|max_length[200]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dokumentasi = upload_file($this->request->getFile('dokumentasi'), 'logbook', ['jpg', 'jpeg', 'png']);

        if (! $dokumentasi) {
            return redirect()->back()->withInput()->with('error', 'Foto dokumentasi wajib diunggah (jpg/png, max 5MB).');
        }

        model(LogbookModel::class)->insert([
            'mahasiswa_id'    => $mhs['id'],
            'tanggal'         => $this->request->getPost('tanggal'),
            'kegiatan'        => $this->request->getPost('kegiatan'),
            'lokasi_kegiatan' => $this->request->getPost('lokasi_kegiatan'),
            'dokumentasi'     => $dokumentasi,
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

    public function delete(int $id)
    {
        $mhs = model(MahasiswaModel::class)->findByUserId(current_user()['id']);

        if (! $mhs) {
            return redirect()->to('/mahasiswa/dashboard');
        }

        $logbookModel = model(LogbookModel::class);
        $logbook = $logbookModel->find($id);

        if (! $logbook || $logbook['mahasiswa_id'] != $mhs['id']) {
            return redirect()->to('/mahasiswa/logbook')->with('error', 'Logbook tidak ditemukan.');
        }

        // Logical rule: Can't delete if already 'diterima'
        if ($logbook['status'] === 'diterima') {
            return redirect()->to('/mahasiswa/logbook')->with('error', 'Logbook yang sudah diterima tidak dapat dihapus.');
        }

        if (!empty($logbook['dokumentasi']) && file_exists(FCPATH . 'uploads/' . $logbook['dokumentasi'])) {
            @unlink(FCPATH . 'uploads/' . $logbook['dokumentasi']);
        }

        $logbookModel->delete($id);

        return redirect()->to('/mahasiswa/logbook')->with('success', 'Logbook berhasil dihapus.');
    }
}
