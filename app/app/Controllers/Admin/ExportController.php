<?php

namespace App\Controllers\Admin;

use App\Controllers\PanelController;
use App\Libraries\AuditLib;
use App\Libraries\ExportLib;
use App\Models\KelompokKknModel;
use App\Models\LaporanModel;
use App\Models\LogbookModel;
use App\Models\MahasiswaModel;
use App\Models\PenilaianModel;

class ExportController extends PanelController
{
    public function index()
    {
        return $this->render('admin/export/index', [
            'title' => 'Export Laporan',
        ]);
    }

    public function mahasiswa()
    {
        $rows = model(MahasiswaModel::class)->getAllWithRelations();
        $data = [];

        foreach ($rows as $r) {
            $data[] = [
                $r['npm'] ?? '',
                $r['nama'] ?? '',
                $r['prodi'] ?? '',
                $r['email'] ?? '',
                $r['nama_kelompok'] ?? '',
                $r['nama_desa'] ?? '',
                $r['no_hp'] ?? '',
            ];
        }

        AuditLib::log('export', 'mahasiswa', 'Export data mahasiswa (' . count($data) . ' baris)');

        return ExportLib::download('mahasiswa_kkn_' . date('Ymd'), [
            'NPM', 'Nama', 'Prodi', 'Email', 'Kelompok', 'Lokasi Desa', 'No HP',
        ], $data);
    }

    public function logbook()
    {
        $rows = model(LogbookModel::class)
            ->select('logbook.*, mahasiswa.npm, mahasiswa.nama as nama_mahasiswa, kelompok_kkn.nama_kelompok')
            ->join('mahasiswa', 'mahasiswa.id = logbook.mahasiswa_id')
            ->join('kelompok_kkn', 'kelompok_kkn.id = mahasiswa.kelompok_id', 'left')
            ->orderBy('logbook.tanggal', 'DESC')
            ->findAll();

        $data = [];

        foreach ($rows as $r) {
            $data[] = [
                $r['npm'] ?? '',
                $r['nama_mahasiswa'] ?? '',
                $r['nama_kelompok'] ?? '',
                $r['tanggal'] ?? '',
                $r['kegiatan'] ?? '',
                $r['lokasi_kegiatan'] ?? '',
                $r['status'] ?? '',
                $r['catatan_dpl'] ?? '',
                $r['validated_at'] ?? '',
            ];
        }

        AuditLib::log('export', 'logbook', 'Export logbook (' . count($data) . ' baris)');

        return ExportLib::download('logbook_kkn_' . date('Ymd'), [
            'NPM', 'Nama', 'Kelompok', 'Tanggal', 'Kegiatan', 'Lokasi Kegiatan', 'Status', 'Catatan DPL', 'Validated At',
        ], $data);
    }

    public function laporan()
    {
        $rows = model(LaporanModel::class)
            ->select('laporan.*, mahasiswa.npm, mahasiswa.nama as nama_mahasiswa, kelompok_kkn.nama_kelompok')
            ->join('mahasiswa', 'mahasiswa.id = laporan.mahasiswa_id')
            ->join('kelompok_kkn', 'kelompok_kkn.id = mahasiswa.kelompok_id', 'left')
            ->orderBy('laporan.created_at', 'DESC')
            ->findAll();

        $data = [];

        foreach ($rows as $r) {
            $data[] = [
                $r['npm'] ?? '',
                $r['nama_mahasiswa'] ?? '',
                $r['nama_kelompok'] ?? '',
                $r['judul'] ?? '',
                $r['status'] ?? '',
                $r['catatan_dpl'] ?? '',
                $r['reviewed_at'] ?? '',
                $r['created_at'] ?? '',
            ];
        }

        AuditLib::log('export', 'laporan', 'Export laporan (' . count($data) . ' baris)');

        return ExportLib::download('laporan_kkn_' . date('Ymd'), [
            'NPM', 'Nama', 'Kelompok', 'Judul', 'Status', 'Catatan DPL', 'Reviewed At', 'Uploaded At',
        ], $data);
    }

    public function nilai()
    {
        $rows = model(PenilaianModel::class)
            ->select('penilaian.*, mahasiswa.npm, mahasiswa.nama as nama_mahasiswa, kelompok_kkn.nama_kelompok, dpl.nama as nama_dpl')
            ->join('mahasiswa', 'mahasiswa.id = penilaian.mahasiswa_id')
            ->join('kelompok_kkn', 'kelompok_kkn.id = mahasiswa.kelompok_id', 'left')
            ->join('dpl', 'dpl.id = penilaian.dpl_id', 'left')
            ->orderBy('penilaian.nilai_akhir', 'DESC')
            ->findAll();

        $data = [];

        foreach ($rows as $r) {
            $data[] = [
                $r['npm'] ?? '',
                $r['nama_mahasiswa'] ?? '',
                $r['nama_kelompok'] ?? '',
                $r['nama_dpl'] ?? '',
                $r['nilai_keaktifan'] ?? '',
                $r['nilai_logbook'] ?? '',
                $r['nilai_laporan'] ?? '',
                $r['nilai_akhir'] ?? '',
                $r['grade'] ?? '',
                $r['prediksi_knn'] ?? '',
                $r['catatan'] ?? '',
            ];
        }

        AuditLib::log('export', 'penilaian', 'Export penilaian (' . count($data) . ' baris)');

        return ExportLib::download('nilai_kkn_' . date('Ymd'), [
            'NPM', 'Nama', 'Kelompok', 'DPL', 'Keaktifan', 'Logbook', 'Laporan', 'Nilai Akhir', 'Grade', 'Prediksi KNN', 'Catatan',
        ], $data);
    }

    public function kelompok()
    {
        $rows = model(KelompokKknModel::class)->getAllWithRelations();
        $data = [];

        foreach ($rows as $r) {
            $data[] = [
                $r['nama_kelompok'] ?? '',
                $r['periode'] ?? '',
                $r['nama_dpl'] ?? '',
                $r['nama_ketua'] ?? '',
                $r['nama_desa'] ?? '',
                $r['kecamatan'] ?? '',
                $r['kabupaten'] ?? '',
                $r['latitude'] ?? '',
                $r['longitude'] ?? '',
                $r['lokasi_gps_at'] ?? '',
                $r['jumlah_anggota'] ?? 0,
            ];
        }

        AuditLib::log('export', 'kelompok_kkn', 'Export kelompok + GPS (' . count($data) . ' baris)');

        return ExportLib::download('kelompok_kkn_' . date('Ymd'), [
            'Kelompok', 'Periode', 'DPL', 'Ketua', 'Desa', 'Kecamatan', 'Kabupaten', 'Latitude', 'Longitude', 'GPS At', 'Anggota',
        ], $data);
    }
}
