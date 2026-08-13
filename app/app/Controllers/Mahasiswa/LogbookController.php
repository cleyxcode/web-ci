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

        $dokumentasi = upload_file($this->request->getFile('dokumentasi'), 'logbook', ['jpg', 'jpeg', 'png']);

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
}
