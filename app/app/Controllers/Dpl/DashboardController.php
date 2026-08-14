<?php

namespace App\Controllers\Dpl;

use App\Controllers\PanelController;
use App\Models\DplModel;
use App\Models\EvaluasiModel;
use App\Models\LaporanModel;
use App\Models\LogbookModel;
use App\Models\MahasiswaModel;

class DashboardController extends PanelController
{
    public function index()
    {
        $dpl = model(DplModel::class)->findByUserId(current_user()['id']);

        if (! $dpl) {
            return $this->render('dpl/dashboard', [
                'title' => 'Dashboard DPL',
                'dpl'   => null,
            ]);
        }

        $mahasiswaModel = model(MahasiswaModel::class);
        $logbookModel   = model(LogbookModel::class);
        $laporanModel   = model(LaporanModel::class);
        $evaluasiModel  = model(EvaluasiModel::class);

        $mahasiswa = $mahasiswaModel->getByDplId($dpl['id']);

        return $this->render('dpl/dashboard', [
            'title'           => 'Dashboard DPL',
            'dpl'             => $dpl,
            'jumlahMahasiswa' => count($mahasiswa),
            'logbookPending'  => $logbookModel->getPendingByDpl($dpl['id']),
            'laporanPending'  => $laporanModel->getPendingByDpl($dpl['id']),
            'totalEvaluasi'   => $evaluasiModel->countAllEvaluasi((int) $dpl['id']),
            'avgEvaluasi'     => $evaluasiModel->averageRating((int) $dpl['id']),
            'kegiatanTerbaru' => $logbookModel->getByDpl($dpl['id']),
            'petaKelompok'    => model(\App\Models\KelompokKknModel::class)->getWithGps($dpl['id']),
        ]);
    }
}
