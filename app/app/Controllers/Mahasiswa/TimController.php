<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\PanelController;
use App\Libraries\AuditLib;
use App\Models\KelompokKknModel;
use App\Models\MahasiswaModel;
use App\Models\UserModel;

class TimController extends PanelController
{
    public function index()
    {
        $mhs = model(MahasiswaModel::class)->findByUserId(current_user()['id']);

        if (! $mhs) {
            return redirect()->to('/mahasiswa/dashboard')->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        $detail = model(MahasiswaModel::class)->getWithRelations($mhs['id']);
        $kelompokId = (int) ($mhs['kelompok_id'] ?? 0);

        $kelompok = null;
        $anggota  = [];
        $isKetua  = false;

        if ($kelompokId > 0) {
            $kelompok = model(KelompokKknModel::class)->getDetail($kelompokId);
            $anggota  = model(MahasiswaModel::class)->getByKelompokId($kelompokId);
            $isKetua  = $kelompok && (int) ($kelompok['ketua_mahasiswa_id'] ?? 0) === (int) $mhs['id'];
        }

        return $this->render('mahasiswa/tim', [
            'title'     => 'Tim KKN Saya',
            'mahasiswa' => $detail,
            'kelompok'  => $kelompok,
            'anggota'   => $anggota,
            'isKetua'   => $isKetua,
        ]);
    }

    public function setLokasiGps()
    {
        $mhs = model(MahasiswaModel::class)->findByUserId(current_user()['id']);

        if (! $mhs || empty($mhs['kelompok_id'])) {
            return redirect()->to('/mahasiswa/tim')->with('error', 'Anda belum masuk kelompok.');
        }

        $kelompokModel = model(KelompokKknModel::class);
        $kelompok      = $kelompokModel->find($mhs['kelompok_id']);

        if (! $kelompok || (int) ($kelompok['ketua_mahasiswa_id'] ?? 0) !== (int) $mhs['id']) {
            return redirect()->to('/mahasiswa/tim')->with('error', 'Hanya ketua kelompok yang boleh menetapkan lokasi GPS.');
        }

        $lat = $this->request->getPost('latitude');
        $lng = $this->request->getPost('longitude');

        if ($lat === null || $lng === null || $lat === '' || $lng === '') {
            return redirect()->back()->with('error', 'Koordinat GPS tidak valid.');
        }

        $lat = (float) $lat;
        $lng = (float) $lng;

        if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
            return redirect()->back()->with('error', 'Koordinat di luar rentang yang diizinkan.');
        }

        $lama = [
            'latitude'  => $kelompok['latitude'] ?? null,
            'longitude' => $kelompok['longitude'] ?? null,
        ];

        $kelompokModel->update($kelompok['id'], [
            'latitude'      => $lat,
            'longitude'     => $lng,
            'lokasi_gps_at' => date('Y-m-d H:i:s'),
        ]);

        AuditLib::log(
            'set_gps',
            'kelompok_kkn',
            'Ketua menetapkan lokasi GPS kelompok ' . $kelompok['nama_kelompok'],
            (int) $kelompok['id'],
            $lama,
            ['latitude' => $lat, 'longitude' => $lng]
        );

        // Notifikasi ke anggota tim & admin
        $anggota = model(MahasiswaModel::class)->getByKelompokId((int) $kelompok['id']);

        foreach ($anggota as $a) {
            if ((int) $a['id'] === (int) $mhs['id']) {
                continue;
            }

            $this->notify(
                (int) $a['user_id'],
                'Lokasi KKN ditetapkan',
                'Ketua ' . $mhs['nama'] . ' menetapkan lokasi GPS kelompok di peta.',
                'info'
            );
        }

        $admins = model(UserModel::class)->where('role', 'admin')->findAll();

        foreach ($admins as $admin) {
            $this->notify(
                (int) $admin['id'],
                'GPS kelompok baru',
                $kelompok['nama_kelompok'] . ' menetapkan lokasi GPS lapangan.',
                'success'
            );
        }

        $this->notifyDplOfMahasiswa(
            $mhs,
            'GPS kelompok ditetapkan',
            $kelompok['nama_kelompok'] . ' menetapkan lokasi GPS lapangan.',
            'info'
        );

        return redirect()->to('/mahasiswa/tim')->with('success', 'Lokasi GPS kelompok berhasil disimpan. Semua anggota dan admin dapat melihatnya di peta.');
    }
}
