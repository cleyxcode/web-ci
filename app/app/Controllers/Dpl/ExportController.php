<?php

namespace App\Controllers\Dpl;

use App\Controllers\PanelController;
use App\Libraries\AuditLib;
use App\Libraries\ExportLib;
use App\Models\DplModel;
use App\Models\LaporanModel;
use App\Models\LogbookModel;
use App\Models\MahasiswaModel;
use App\Models\PenilaianModel;

class ExportController extends PanelController
{
    public function index()
    {
        return $this->render('dpl/export/index', [
            'title' => 'Export Laporan',
        ]);
    }

    public function logbook()
    {
        $dpl = model(DplModel::class)->findByUserId(current_user()['id']);

        if (! $dpl) {
            return redirect()->to('/dpl/dashboard');
        }

        $rows = model(LogbookModel::class)->getByDpl($dpl['id']);
        $data = [];

        foreach ($rows as $r) {
            $data[] = [
                $r['npm'] ?? '',
                $r['nama_mahasiswa'] ?? $r['nama'] ?? '',
                $r['tanggal'] ?? '',
                $r['kegiatan'] ?? '',
                $r['lokasi_kegiatan'] ?? '',
                $r['status'] ?? '',
                $r['catatan_dpl'] ?? '',
            ];
        }

        AuditLib::log('export', 'logbook', 'DPL export logbook bimbingan (' . count($data) . ' baris)');

        return ExportLib::download('logbook_bimbingan_' . date('Ymd'), [
            'NPM', 'Nama', 'Tanggal', 'Kegiatan', 'Lokasi', 'Status', 'Catatan',
        ], $data);
    }

    public function laporan()
    {
        $dpl = model(DplModel::class)->findByUserId(current_user()['id']);

        if (! $dpl) {
            return redirect()->to('/dpl/dashboard');
        }

        $rows = model(LaporanModel::class)->getByDpl($dpl['id']);
        $data = [];

        foreach ($rows as $r) {
            $data[] = [
                $r['npm'] ?? '',
                $r['nama_mahasiswa'] ?? $r['nama'] ?? '',
                $r['judul'] ?? '',
                $r['status'] ?? '',
                $r['catatan_dpl'] ?? '',
                $r['created_at'] ?? '',
            ];
        }

        AuditLib::log('export', 'laporan', 'DPL export laporan bimbingan (' . count($data) . ' baris)');

        return ExportLib::download('laporan_bimbingan_' . date('Ymd'), [
            'NPM', 'Nama', 'Judul', 'Status', 'Catatan', 'Uploaded At',
        ], $data);
    }

    public function nilai()
    {
        $dpl = model(DplModel::class)->findByUserId(current_user()['id']);

        if (! $dpl) {
            return redirect()->to('/dpl/dashboard');
        }

        $mahasiswa = model(MahasiswaModel::class)->getByDplId($dpl['id']);
        $penilaianModel = model(PenilaianModel::class);
        $data = [];

        foreach ($mahasiswa as $m) {
            $n = $penilaianModel->findByMahasiswa((int) $m['id']);
            $data[] = [
                $m['npm'] ?? '',
                $m['nama'] ?? '',
                $n['nilai_keaktifan'] ?? '',
                $n['nilai_logbook'] ?? '',
                $n['nilai_laporan'] ?? '',
                $n['nilai_akhir'] ?? '',
                $n['grade'] ?? '',
                $n['catatan'] ?? '',
            ];
        }

        AuditLib::log('export', 'penilaian', 'DPL export nilai bimbingan (' . count($data) . ' baris)');

        return ExportLib::download('nilai_bimbingan_' . date('Ymd'), [
            'NPM', 'Nama', 'Keaktifan', 'Logbook', 'Laporan', 'Nilai Akhir', 'Grade', 'Catatan',
        ], $data);
    }
}
