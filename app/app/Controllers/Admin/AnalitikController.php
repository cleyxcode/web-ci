<?php

namespace App\Controllers\Admin;

use App\Controllers\PanelController;
use App\Models\KelompokKknModel;
use App\Models\LaporanModel;
use App\Models\LogbookModel;
use App\Models\MahasiswaModel;
use App\Models\PenilaianModel;

class AnalitikController extends PanelController
{
    public function index()
    {
        $penilaianModel = model(PenilaianModel::class);
        $logbookModel   = model(LogbookModel::class);
        $laporanModel   = model(LaporanModel::class);
        $kelompokModel  = model(KelompokKknModel::class);

        $nilai = $penilaianModel
            ->select('penilaian.*, mahasiswa.nama as nama_mahasiswa, mahasiswa.npm')
            ->join('mahasiswa', 'mahasiswa.id = penilaian.mahasiswa_id')
            ->findAll();

        $gradeDist = [];
        $knnDist   = [];
        $knnMatch  = 0;
        $knnTotal  = 0;

        foreach ($nilai as $n) {
            $g = $n['grade'] ?? '-';
            $gradeDist[$g] = ($gradeDist[$g] ?? 0) + 1;

            if (! empty($n['prediksi_knn'])) {
                $k = $n['prediksi_knn'];
                $knnDist[$k] = ($knnDist[$k] ?? 0) + 1;
            }

            if (! empty($n['prediksi_knn']) && ! empty($n['grade'])) {
                $knnTotal++;
                if ($n['prediksi_knn'] === $n['grade']) {
                    $knnMatch++;
                }
            }
        }

        $kelengkapan = [];

        foreach ($kelompokModel->getAllWithRelations() as $k) {
            $anggota = model(MahasiswaModel::class)->getByKelompokId((int) $k['id']);
            $totalLog = 0;
            $validLog = 0;
            $totalLap = 0;

            foreach ($anggota as $m) {
                $totalLog += $logbookModel->where('mahasiswa_id', $m['id'])->countAllResults(false);
                $validLog += $logbookModel->countByMahasiswaStatus((int) $m['id'], 'divalidasi');
                $totalLap += $laporanModel->where('mahasiswa_id', $m['id'])->countAllResults(false);
            }

            $kelengkapan[] = [
                'nama'       => $k['nama_kelompok'],
                'anggota'    => count($anggota),
                'logbook'    => $totalLog,
                'valid'      => $validLog,
                'laporan'    => $totalLap,
                'ada_gps'    => ! empty($k['latitude']),
            ];
        }

        $logStatus = $logbookModel->select('status, COUNT(*) as total')->groupBy('status')->findAll();

        return $this->render('admin/analitik/index', [
            'title'         => 'Analitik & KNN',
            'gradeDist'     => $gradeDist,
            'knnDist'       => $knnDist,
            'knnAkurasi'    => $knnTotal > 0 ? round(($knnMatch / $knnTotal) * 100, 1) : null,
            'knnMatch'      => $knnMatch,
            'knnTotal'      => $knnTotal,
            'nilai'         => $nilai,
            'kelengkapan'   => $kelengkapan,
            'logStatus'     => $logStatus,
            'petaKelompok'  => $kelompokModel->getWithGps(),
        ]);
    }
}
