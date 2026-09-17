<?php

namespace App\Controllers\Admin;

use App\Controllers\PanelController;
use App\Models\LaporanModel;

class LaporanController extends PanelController
{
    public function index()
    {
        return $this->render('admin/laporan/index', [
            'title'   => 'Semua Laporan',
            'laporan' => model(LaporanModel::class)->getAllWithMahasiswa(),
        ]);
    }
}
