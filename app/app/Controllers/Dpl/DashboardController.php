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
        $kelompokModel  = model(\App\Models\KelompokKknModel::class);
        $filterPeriod   = $logbookModel->normalizePeriod($this->request->getGet('periode') ?: 'minggu');
        $filterDate     = $logbookModel->normalizeAnchorDate($this->request->getGet('tanggal'));
        $filterRange    = $logbookModel->resolvePeriod($filterPeriod, $filterDate);
        $filterLabel    = $filterPeriod === 'hari'
            ? 'Hari ' . format_tanggal($filterRange['start'])
            : ($filterPeriod === 'minggu'
                ? 'Minggu ' . format_tanggal($filterRange['start']) . ' – ' . format_tanggal($filterRange['end'])
                : 'Semua periode');

        $mahasiswa = $mahasiswaModel->getByDplId($dpl['id']);

        return $this->render('dpl/dashboard', [
            'title'           => 'Dashboard DPL',
            'dpl'             => $dpl,
            'jumlahMahasiswa' => count($mahasiswa),
            'logbookPending'  => $logbookModel->getPendingByDpl($dpl['id']),
            'laporanPending'  => $laporanModel->getPendingByDpl($dpl['id']),
            'totalEvaluasi'   => $evaluasiModel->countAllEvaluasi((int) $dpl['id']),
            'avgEvaluasi'     => $evaluasiModel->averageRating((int) $dpl['id']),
            'kegiatanTerbaru' => $logbookModel->getByDpl($dpl['id'], null, 'DESC', $filterPeriod, $filterDate),
            'filterPeriod'    => $filterPeriod,
            'filterDate'      => $filterDate,
            'filterLabel'     => $filterLabel,
            'kelompok'        => $kelompokModel->getByDplId((int) $dpl['id']),
            'petaKelompok'    => $kelompokModel->getWithGps($dpl['id']),
        ]);
    }
}
