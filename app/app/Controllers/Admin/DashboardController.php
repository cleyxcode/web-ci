<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\PanelController;
use App\Models\DplModel;
use App\Models\KelompokKknModel;
use App\Models\LaporanModel;
use App\Models\LogbookModel;
use App\Models\LokasiKknModel;
use App\Models\MahasiswaModel;

class DashboardController extends PanelController
{
    public function index()
    {
        $mahasiswaModel = model(MahasiswaModel::class);
        $dplModel       = model(DplModel::class);
        $lokasiModel    = model(LokasiKknModel::class);
        $logbookModel   = model(LogbookModel::class);
        $laporanModel   = model(LaporanModel::class);
        $filterPeriod   = $logbookModel->normalizePeriod($this->request->getGet('periode') ?: 'minggu');
        $filterDate     = $logbookModel->normalizeAnchorDate($this->request->getGet('tanggal'));
        $filterRange    = $logbookModel->resolvePeriod($filterPeriod, $filterDate);
        $filterLabel    = $filterPeriod === 'hari'
            ? 'Hari ' . format_tanggal($filterRange['start'])
            : ($filterPeriod === 'minggu'
                ? 'Minggu ' . format_tanggal($filterRange['start']) . ' – ' . format_tanggal($filterRange['end'])
                : 'Semua periode');

        $laporanStatus = $laporanModel->select('status, COUNT(*) as total')
            ->groupBy('status')
            ->findAll();

        return $this->render('admin/dashboard', [
            'title'            => 'Dashboard Admin',
            'totalMahasiswa'   => $mahasiswaModel->countAllResults(),
            'totalDpl'         => $dplModel->countAllResults(),
            'totalLokasi'      => $lokasiModel->countAllResults(),
            'totalKelompok'    => model(KelompokKknModel::class)->countAllResults(),
            'totalLogbook'     => $logbookModel->countAllResults(),
            'filteredLogbook'  => $logbookModel->countByPeriod($filterPeriod, $filterDate),
            'totalLaporan'     => $laporanModel->countAllResults(),
            'mahasiswaTerbaru' => $mahasiswaModel->getAllWithRelations(),
            'logbookPerMinggu' => $logbookModel->getDashboardSeries($filterPeriod, $filterDate),
            'filterPeriod'     => $filterPeriod,
            'filterDate'       => $filterDate,
            'filterLabel'      => $filterLabel,
            'laporanStatus'    => $laporanStatus,
            'petaKelompok'     => model(KelompokKknModel::class)->getWithGps(),
        ]);
    }
}
