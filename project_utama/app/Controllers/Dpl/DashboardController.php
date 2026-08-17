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
                'title' => 'Dashboard Dosen Pembimbing Lapangan',
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
        $allLogbook = $logbookModel->getByDpl((int) $dpl['id']);
        $statusChart = [];
        foreach (['menunggu' => 'Menunggu', 'divalidasi' => 'Divalidasi', 'ditolak' => 'Ditolak'] as $status => $label) {
            $statusChart[] = ['label' => $label, 'total' => count(array_filter($allLogbook, static fn (array $row): bool => ($row['status'] ?? '') === $status))];
        }
        $completionChart = [
            ['label' => 'Sudah dievaluasi', 'total' => (int) $evaluasiModel->countAllEvaluasi((int) $dpl['id'])],
            ['label' => 'Belum dievaluasi', 'total' => max(0, count($mahasiswa) - (int) $evaluasiModel->countAllEvaluasi((int) $dpl['id']))],
        ];
        $activityByDate = [];
        foreach ($allLogbook as $row) {
            $date = (string) ($row['tanggal'] ?? '');
            if ($date !== '') $activityByDate[$date] = ($activityByDate[$date] ?? 0) + 1;
        }
        $activityChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime('-' . $i . ' days'));
            $activityChart[] = ['label' => date('d M', strtotime($date)), 'total' => (int) ($activityByDate[$date] ?? 0)];
        }

        return $this->render('dpl/dashboard', [
            'title'           => 'Dashboard Dosen Pembimbing Lapangan',
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
            'statusChart'      => $statusChart,
            'completionChart'  => $completionChart,
            'activityChart'    => $activityChart,
        ]);
    }
}
