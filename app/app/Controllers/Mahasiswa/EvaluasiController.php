<?php

declare(strict_types=1);

namespace App\Controllers\Mahasiswa;

use App\Controllers\PanelController;
use App\Models\EvaluasiModel;
use App\Models\MahasiswaModel;

final class EvaluasiController extends PanelController
{
    public function index(): string
    {
        $mahasiswa = model(MahasiswaModel::class)->findByUserId((int) current_user()['id']);

        if ($mahasiswa === null) {
            return redirect()->to('/mahasiswa/dashboard')->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        $detail = model(MahasiswaModel::class)->getWithRelations((int) $mahasiswa['id']);

        return $this->render('mahasiswa/evaluasi/index', [
            'title'     => 'Evaluasi DPL',
            'mahasiswa' => $detail,
            'evaluasi'  => model(EvaluasiModel::class)->findByMahasiswaDpl((int) $mahasiswa['id']),
        ]);
    }
}
