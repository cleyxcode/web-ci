<?php

declare(strict_types=1);

namespace App\Controllers\Dpl;

use App\Controllers\PanelController;
use App\Models\DplModel;
use App\Models\EvaluasiModel;
use App\Models\MahasiswaModel;

class EvaluasiController extends PanelController
{
    public function index()
    {
        $dpl = model(DplModel::class)->findByUserId((int) current_user()['id']);

        if (! $dpl) {
            return redirect()->to('/dpl/dashboard')->with('error', 'Profil DPL tidak ditemukan.');
        }

        $evaluasiModel = model(EvaluasiModel::class);
        $mahasiswa     = model(MahasiswaModel::class)->getByDplId((int) $dpl['id']);
        $evaluasi      = $evaluasiModel->getByDpl((int) $dpl['id']);
        $submittedIds  = array_column($evaluasi, 'mahasiswa_id');

        $belumEvaluasi = array_values(array_filter(
            $mahasiswa,
            static fn (array $row): bool => ! in_array($row['id'], $submittedIds, false)
        ));

        return $this->render('dpl/evaluasi/index', [
            'title'          => 'Evaluasi Mahasiswa',
            'evaluasi'       => $evaluasi,
            'belumEvaluasi'  => $belumEvaluasi,
            'avgRating'      => $evaluasiModel->averageRating((int) $dpl['id']),
            'totalEvaluasi'  => count($evaluasi),
            'totalMahasiswa' => count($mahasiswa),
        ]);
    }
}
