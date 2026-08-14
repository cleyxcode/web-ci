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

        $logbookPerMinggu = $logbookModel->select("DATE_FORMAT(tanggal, '%Y-%u') as minggu, COUNT(*) as total")
            ->groupBy('minggu')
            ->orderBy('minggu', 'ASC')
            ->findAll(8);

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
            'totalLaporan'     => $laporanModel->countAllResults(),
            'mahasiswaTerbaru' => $mahasiswaModel->getAllWithRelations(),
            'logbookPerMinggu' => $logbookPerMinggu,
            'laporanStatus'    => $laporanStatus,
            'petaKelompok'     => model(KelompokKknModel::class)->getWithGps(),
        ]);
    }
}
