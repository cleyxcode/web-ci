<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\PanelController;
use App\Models\EvaluasiModel;
use App\Models\MahasiswaModel;

class EvaluasiController extends PanelController
{
    public function index()
    {
        $evaluasiModel = model(EvaluasiModel::class);
        $evaluasi      = $evaluasiModel->getAllWithMahasiswa();
        $totalMhs      = model(MahasiswaModel::class)->countAllResults();

        return $this->render('admin/evaluasi/index', [
            'title'         => 'Evaluasi Kegiatan',
            'evaluasi'      => $evaluasi,
            'avgRating'     => $evaluasiModel->averageRating(),
            'totalEvaluasi' => count($evaluasi),
            'totalMahasiswa'=> $totalMhs,
        ]);
    }
}
