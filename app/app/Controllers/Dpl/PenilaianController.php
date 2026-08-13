<?php

namespace App\Controllers\Dpl;

use App\Controllers\PanelController;
use App\Libraries\AuditLib;
use App\Libraries\KnnLib;
use App\Models\DplModel;
use App\Models\LogbookModel;
use App\Models\LaporanModel;
use App\Models\MahasiswaModel;
use App\Models\PenilaianModel;

class PenilaianController extends PanelController
{
    public function index()
    {
        $dpl = model(DplModel::class)->findByUserId(current_user()['id']);

        if (! $dpl) {
            return redirect()->to('/dpl/dashboard');
        }

        return $this->render('dpl/penilaian/index', [
            'title'     => 'Penilaian Mahasiswa',
            'mahasiswa' => model(MahasiswaModel::class)->getByDplId($dpl['id']),
            'dpl'       => $dpl,
        ]);
    }

    public function form(int $mahasiswaId)
    {
        $dpl = model(DplModel::class)->findByUserId(current_user()['id']);
        $mhs = model(MahasiswaModel::class)->getWithRelations($mahasiswaId);

        if (! $dpl || ! $mhs) {
            return redirect()->to('/dpl/penilaian')->with('error', 'Data tidak ditemukan.');
        }

        $penilaianModel = model(PenilaianModel::class);
        $logbookModel   = model(LogbookModel::class);
        $laporanModel   = model(LaporanModel::class);

        $jmlLogbook       = $logbookModel->where('mahasiswa_id', $mahasiswaId)->countAllResults();
        $jmlLogbookValid  = $logbookModel->countByMahasiswaStatus($mahasiswaId, 'divalidasi');
        $jmlLaporan       = $laporanModel->where('mahasiswa_id', $mahasiswaId)->countAllResults();
        $jmlLaporanTerima = $laporanModel->where('mahasiswa_id', $mahasiswaId)->where('status', 'diterima')->countAllResults();

        $existing = $penilaianModel->findByMahasiswa($mahasiswaId);
        $keaktifan = $existing['nilai_keaktifan'] ?? 0;

        $knn = new KnnLib();
        $training = [];

        foreach ($penilaianModel->getTrainingData() as $row) {
            $training[] = [
                'features' => [
                    (float) $logbookModel->where('mahasiswa_id', $row['mahasiswa_id'])->countAllResults(),
                    (float) $logbookModel->countByMahasiswaStatus($row['mahasiswa_id'], 'divalidasi'),
                    (float) $laporanModel->where('mahasiswa_id', $row['mahasiswa_id'])->countAllResults(),
                    (float) $laporanModel->where('mahasiswa_id', $row['mahasiswa_id'])->where('status', 'diterima')->countAllResults(),
                    (float) $row['nilai_keaktifan'],
                ],
                'grade' => $row['grade'],
            ];
        }

        $knn->setTrainingData($training);
        $prediksi = $knn->predict([$jmlLogbook, $jmlLogbookValid, $jmlLaporan, $jmlLaporanTerima, (float) $keaktifan]);

        return $this->render('dpl/penilaian/form', [
            'title'            => 'Penilaian - ' . $mhs['nama'],
            'mahasiswa'        => $mhs,
            'penilaian'        => $existing,
            'prediksi_knn'     => $prediksi,
            'jml_logbook'      => $jmlLogbook,
            'jml_logbook_valid'=> $jmlLogbookValid,
            'jml_laporan'      => $jmlLaporan,
            'jml_laporan_terima'=> $jmlLaporanTerima,
            'dpl'              => $dpl,
        ]);
    }

    public function save(int $mahasiswaId)
    {
        $dpl = model(DplModel::class)->findByUserId(current_user()['id']);

        if (! $dpl) {
            return redirect()->to('/dpl/penilaian');
        }

        $keaktifan = (float) $this->request->getPost('nilai_keaktifan');
        $logbook   = (float) $this->request->getPost('nilai_logbook');
        $laporan   = (float) $this->request->getPost('nilai_laporan');
        $nilaiAkhir = KnnLib::hitungNilaiAkhir($keaktifan, $logbook, $laporan);
        $grade      = $this->request->getPost('grade') ?: KnnLib::gradeFromScore($nilaiAkhir);

        $penilaianModel = model(PenilaianModel::class);
        $existing       = $penilaianModel->findByMahasiswa($mahasiswaId);

        $data = [
            'mahasiswa_id'    => $mahasiswaId,
            'dpl_id'          => $dpl['id'],
            'nilai_keaktifan' => $keaktifan,
            'nilai_logbook'   => $logbook,
            'nilai_laporan'   => $laporan,
            'nilai_akhir'     => $nilaiAkhir,
            'grade'           => $grade,
            'prediksi_knn'    => $this->request->getPost('prediksi_knn'),
            'catatan'         => $this->request->getPost('catatan'),
        ];

        if ($existing) {
            $penilaianModel->update($existing['id'], $data);
        } else {
            $penilaianModel->insert($data);
        }

        $mhs = model(MahasiswaModel::class)->find($mahasiswaId);

        AuditLib::log(
            $existing ? 'update_nilai' : 'publish_nilai',
            'penilaian',
            'Nilai ' . ($mhs['nama'] ?? '') . ': ' . $nilaiAkhir . ' (Grade ' . $grade . ')',
            $existing ? (int) $existing['id'] : null,
            $existing ? [
                'nilai_akhir' => $existing['nilai_akhir'] ?? null,
                'grade'       => $existing['grade'] ?? null,
            ] : null,
            ['nilai_akhir' => $nilaiAkhir, 'grade' => $grade, 'prediksi_knn' => $data['prediksi_knn']]
        );

        $this->notify(
            $mhs['user_id'],
            'Nilai Dipublikasikan',
            'Nilai akhir Anda: ' . $nilaiAkhir . ' (Grade ' . $grade . ')',
            'success',
            'nilai.published',
            ['nilai_akhir' => $nilaiAkhir, 'grade' => $grade]
        );

        $this->notifyAdmins(
            'Nilai dipublikasikan',
            ($mhs['nama'] ?? 'Mahasiswa') . ' mendapat nilai ' . $nilaiAkhir . ' (Grade ' . $grade . ').',
            'success'
        );

        return redirect()->to('/dpl/penilaian')->with('success', 'Penilaian disimpan.');
    }
}
