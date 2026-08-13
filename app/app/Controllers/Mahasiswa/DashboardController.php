<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\PanelController;
use App\Models\LogbookModel;
use App\Models\MahasiswaModel;
use App\Models\PengumumanModel;

class DashboardController extends PanelController
{
    public function index()
    {
        $mhs = model(MahasiswaModel::class)->findByUserId(current_user()['id']);
        $detail = $mhs ? model(MahasiswaModel::class)->getWithRelations($mhs['id']) : null;

        $logbookModel = model(LogbookModel::class);
        $totalLogbook = $mhs ? $logbookModel->where('mahasiswa_id', $mhs['id'])->countAllResults() : 0;
        $validLogbook = $mhs ? $logbookModel->countByMahasiswaStatus($mhs['id'], 'divalidasi') : 0;
        $progress     = $totalLogbook > 0 ? round(($validLogbook / max($totalLogbook, 1)) * 100) : 0;

        return $this->render('mahasiswa/dashboard', [
            'title'         => 'Dashboard Mahasiswa',
            'mahasiswa'     => $detail,
            'progress'      => $progress,
            'totalLogbook'  => $totalLogbook,
            'validLogbook'  => $validLogbook,
            'totalLaporan'  => $mhs ? model(\App\Models\LaporanModel::class)->where('mahasiswa_id', $mhs['id'])->countAllResults() : 0,
            'logbookTerbaru'=> $mhs ? $logbookModel->getByMahasiswa($mhs['id']) : [],
            'pengumuman'    => model(PengumumanModel::class)->getLatest(3),
            'petaKelompok'  => $detail && ! empty($detail['latitude']) ? [[
                'latitude'       => $detail['latitude'],
                'longitude'      => $detail['longitude'],
                'nama_kelompok'  => $detail['nama_kelompok'] ?? 'Lokasi KKN',
                'nama_desa'      => $detail['nama_desa'] ?? '',
                'nama_ketua'     => '',
            ]] : [],
        ]);
    }
}
