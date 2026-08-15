<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\PanelController;
use App\Models\EvaluasiModel;
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
        $filterPeriod = $logbookModel->normalizePeriod($this->request->getGet('periode') ?: 'minggu');
        $filterDate   = $logbookModel->normalizeAnchorDate($this->request->getGet('tanggal'));
        $filterRange  = $logbookModel->resolvePeriod($filterPeriod, $filterDate);
        $filterLabel  = $filterPeriod === 'hari'
            ? 'Hari ' . format_tanggal($filterRange['start'])
            : ($filterPeriod === 'minggu'
                ? 'Minggu ' . format_tanggal($filterRange['start']) . ' – ' . format_tanggal($filterRange['end'])
                : 'Semua periode');
        $totalLogbook = $mhs ? $logbookModel->countByMahasiswaPeriod($mhs['id'], $filterPeriod, $filterDate) : 0;
        $validLogbook = $mhs ? $logbookModel->countByMahasiswaStatus($mhs['id'], 'divalidasi', $filterPeriod, $filterDate) : 0;
        $progress     = $totalLogbook > 0 ? round(($validLogbook / max($totalLogbook, 1)) * 100) : 0;
        $evaluasi     = $mhs ? model(EvaluasiModel::class)->findByMahasiswa((int) $mhs['id']) : null;

        return $this->render('mahasiswa/dashboard', [
            'title'         => 'Dashboard Mahasiswa',
            'mahasiswa'     => $detail,
            'progress'      => $progress,
            'totalLogbook'  => $totalLogbook,
            'validLogbook'  => $validLogbook,
            'totalLaporan'  => $mhs ? model(\App\Models\LaporanModel::class)->where('mahasiswa_id', $mhs['id'])->countAllResults() : 0,
            'evaluasi'      => $evaluasi,
            'logbookTerbaru'=> $mhs ? $logbookModel->getByMahasiswa($mhs['id'], $filterPeriod, $filterDate) : [],
            'filterPeriod'  => $filterPeriod,
            'filterDate'    => $filterDate,
            'filterLabel'   => $filterLabel,
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
