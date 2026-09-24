<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\PanelController;
use App\Models\LogbookModel;

final class LogbookController extends PanelController
{
    public function index()
    {
        $status = $this->request->getGet('status');
        $status = in_array($status, ['menunggu', 'divalidasi', 'ditolak'], true) ? $status : null;
        $model = model(LogbookModel::class);
        $builder = $model->select('logbook.*, mahasiswa.nama as nama_mahasiswa, mahasiswa.npm,
                kelompok_kkn.nama_kelompok, dpl.nama as nama_dpl,
                lokasi_kkn.nama_desa, lokasi_kkn.kecamatan, lokasi_kkn.kabupaten')
            ->join('mahasiswa', 'mahasiswa.id = logbook.mahasiswa_id')
            ->join('kelompok_kkn', 'kelompok_kkn.id = mahasiswa.kelompok_id', 'left')
            ->join('dpl', 'dpl.id = kelompok_kkn.dpl_id', 'left')
            ->join('lokasi_kkn', 'lokasi_kkn.id = kelompok_kkn.lokasi_id', 'left');
        if ($status !== null) $builder->where('logbook.status', $status);

        return $this->render('admin/logbook/index', [
            'title' => 'Logbook Mahasiswa',
            'logbooks' => $builder->orderBy('logbook.tanggal', 'DESC')->findAll(),
            'filterStatus' => $status,
            'total' => $model->countAllResults(),
        ]);
    }
}
