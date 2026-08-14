<?php

declare(strict_types=1);

namespace App\Controllers\Dpl;

use App\Controllers\PanelController;
use App\Libraries\AuditLib;
use App\Libraries\NilaiLib;
use App\Models\DplModel;
use App\Models\EvaluasiModel;
use App\Models\LaporanModel;
use App\Models\LogbookModel;
use App\Models\MahasiswaModel;
use App\Models\PenilaianModel;

class PenilaianController extends PanelController
{
    public function index()
    {
        $dpl = model(DplModel::class)->findByUserId((int) current_user()['id']);

        if (! $dpl) {
            return redirect()->to('/dpl/dashboard');
        }

        $mahasiswa = model(MahasiswaModel::class)->getByDplId((int) $dpl['id']);
        $penilaianModel = model(PenilaianModel::class);

        foreach ($mahasiswa as &$row) {
            $nilai = $penilaianModel->findByMahasiswa((int) $row['id']);
            $row['sudah_dinilai'] = $nilai !== null;
            $row['nilai_akhir']   = $nilai['nilai_akhir'] ?? null;
            $row['grade']         = $nilai['grade'] ?? null;
        }
        unset($row);

        return $this->render('dpl/penilaian/index', [
            'title'     => 'Penilaian Mahasiswa',
            'mahasiswa' => $mahasiswa,
            'dpl'       => $dpl,
        ]);
    }

    public function form(int $mahasiswaId)
    {
        $dpl = model(DplModel::class)->findByUserId((int) current_user()['id']);
        $mhs = model(MahasiswaModel::class)->getWithRelations($mahasiswaId);

        if (! $dpl || ! $mhs) {
            return redirect()->to('/dpl/penilaian')->with('error', 'Data tidak ditemukan.');
        }

        // Pastikan mahasiswa benar bimbingan DPL ini
        if ((int) ($mhs['dpl_id'] ?? 0) !== (int) $dpl['id']) {
            return redirect()->to('/dpl/penilaian')->with('error', 'Mahasiswa bukan bimbingan Anda.');
        }

        $penilaianModel = model(PenilaianModel::class);
        $logbookModel   = model(LogbookModel::class);
        $laporanModel   = model(LaporanModel::class);

        $jmlLogbook       = $logbookModel->where('mahasiswa_id', $mahasiswaId)->countAllResults();
        $jmlLogbookValid  = $logbookModel->countByMahasiswaStatus($mahasiswaId, 'divalidasi');
        $jmlLaporan       = $laporanModel->where('mahasiswa_id', $mahasiswaId)->countAllResults();
        $jmlLaporanTerima = $laporanModel->where('mahasiswa_id', $mahasiswaId)->where('status', 'diterima')->countAllResults();
        $existing         = $penilaianModel->findByMahasiswa($mahasiswaId);
        $evaluasi         = model(EvaluasiModel::class)->findByMahasiswa($mahasiswaId);

        return $this->render('dpl/penilaian/form', [
            'title'             => 'Penilaian - ' . $mhs['nama'],
            'mahasiswa'         => $mhs,
            'penilaian'         => $existing,
            'evaluasi'          => $evaluasi,
            'jml_logbook'       => $jmlLogbook,
            'jml_logbook_valid' => $jmlLogbookValid,
            'jml_laporan'       => $jmlLaporan,
            'jml_laporan_terima'=> $jmlLaporanTerima,
            'dpl'               => $dpl,
        ]);
    }

    public function save(int $mahasiswaId)
    {
        $dpl = model(DplModel::class)->findByUserId((int) current_user()['id']);
        $mhs = model(MahasiswaModel::class)->getWithRelations($mahasiswaId);

        if (! $dpl || ! $mhs || (int) ($mhs['dpl_id'] ?? 0) !== (int) $dpl['id']) {
            return redirect()->to('/dpl/penilaian')->with('error', 'Data tidak valid.');
        }

        $rules = [
            'nilai_keaktifan' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
            'nilai_logbook'   => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
            'nilai_laporan'   => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
            'grade'           => 'permit_empty|in_list[A,B,BC,C,D]',
            'catatan'         => 'permit_empty|max_length[2000]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $keaktifan  = (float) $this->request->getPost('nilai_keaktifan');
        $logbook    = (float) $this->request->getPost('nilai_logbook');
        $laporan    = (float) $this->request->getPost('nilai_laporan');
        $nilaiAkhir = NilaiLib::hitungNilaiAkhir($keaktifan, $logbook, $laporan);
        $grade      = (string) ($this->request->getPost('grade') ?: NilaiLib::gradeFromScore($nilaiAkhir));

        $penilaianModel = model(PenilaianModel::class);
        $existing       = $penilaianModel->findByMahasiswa($mahasiswaId);

        $data = [
            'mahasiswa_id'    => $mahasiswaId,
            'dpl_id'          => (int) $dpl['id'],
            'nilai_keaktifan' => $keaktifan,
            'nilai_logbook'   => $logbook,
            'nilai_laporan'   => $laporan,
            'nilai_akhir'     => $nilaiAkhir,
            'grade'           => $grade,
            'catatan'         => $this->request->getPost('catatan'),
        ];

        if ($existing) {
            $penilaianModel->update((int) $existing['id'], $data);
            $penilaianId = (int) $existing['id'];
        } else {
            $penilaianId = (int) $penilaianModel->insert($data);
        }

        AuditLib::log(
            $existing ? 'update_nilai' : 'publish_nilai',
            'penilaian',
            'Nilai ' . ($mhs['nama'] ?? '') . ': ' . $nilaiAkhir . ' (Grade ' . $grade . ')',
            $penilaianId,
            $existing ? [
                'nilai_akhir' => $existing['nilai_akhir'] ?? null,
                'grade'       => $existing['grade'] ?? null,
            ] : null,
            ['nilai_akhir' => $nilaiAkhir, 'grade' => $grade]
        );

        $this->notify(
            (int) $mhs['user_id'],
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

        return redirect()->to('/dpl/penilaian')->with('success', 'Penilaian disimpan dan dapat dilihat mahasiswa.');
    }
}
