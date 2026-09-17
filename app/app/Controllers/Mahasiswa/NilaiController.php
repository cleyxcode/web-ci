<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\PanelController;
use App\Models\MahasiswaModel;
use App\Models\PenilaianModel;

class NilaiController extends PanelController
{
    public function index()
    {
        $mhs = model(MahasiswaModel::class)->findByUserId(current_user()['id']);
        $nilai = $mhs ? model(PenilaianModel::class)->findByMahasiswa($mhs['id']) : null;

        return $this->render('mahasiswa/nilai', [
            'title' => 'Nilai KKN',
            'nilai' => $nilai,
        ]);
    }
}
