<?php

declare(strict_types=1);

namespace App\Controllers\Mahasiswa;

use App\Controllers\PanelController;
use App\Models\EvaluasiModel;
use App\Models\EvaluasiKriteriaModel;
use App\Models\DplModel;
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
            'criteria'  => model(EvaluasiKriteriaModel::class)->getForDpl(
                (int) ($detail['dpl_id'] ?? 0),
                (int) ($detail['kelompok_id'] ?? 0)
            ),
        ]);
    }

    public function save()
    {
        $user = current_user();
        $mahasiswa = model(MahasiswaModel::class)->findByUserId((int) ($user['id'] ?? 0));
        $detail = $mahasiswa ? model(MahasiswaModel::class)->getWithRelations((int) $mahasiswa['id']) : null;

        if ($mahasiswa === null || $detail === null || empty($detail['dpl_id'])) {
            return redirect()->to('/mahasiswa/evaluasi')->with('error', 'Anda belum memiliki DPL atau kelompok KKN.');
        }

        $criteria = model(EvaluasiKriteriaModel::class)->getForDpl(
            (int) $detail['dpl_id'],
            (int) ($detail['kelompok_id'] ?? 0)
        );
        $ratings = $this->request->getPost('criteria_rating');
        $errors = [];
        $answers = [];
        $total = 0;

        foreach ($criteria as $criterion) {
            $id = (int) $criterion['id'];
            $rating = (int) (($ratings[$id] ?? 0));
            if ($rating < 1 || $rating > 5) {
                $errors['criteria_rating_' . $id] = 'Beri rating 1–5 untuk: ' . $criterion['nama'];
                continue;
            }
            $answers[] = [
                'id' => $id,
                'nama' => (string) $criterion['nama'],
                'deskripsi' => (string) ($criterion['deskripsi'] ?? ''),
                'rating' => $rating,
            ];
            $total += $rating;
        }

        $comment = trim((string) $this->request->getPost('komentar'));
        if (mb_strlen($comment) > 2000) {
            $errors['komentar'] = 'Komentar maksimal 2.000 karakter.';
        }
        if ($errors !== []) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $rating = round($total / max(count($answers), 1), 2);
        $model = model(EvaluasiModel::class);
        $existing = $model->findByMahasiswaDpl((int) $mahasiswa['id']);
        $data = [
            'mahasiswa_id' => (int) $mahasiswa['id'],
            'tipe_evaluasi' => 'dpl',
            'kelompok_id' => (int) $detail['kelompok_id'],
            'dpl_id' => (int) $detail['dpl_id'],
            'penilai_id' => (int) $user['id'],
            'detail_evaluasi' => json_encode($answers, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'rating' => $rating,
            'aspek_bimbingan' => $rating,
            'aspek_lokasi' => $rating,
            'aspek_pelaksanaan' => $rating,
            'skor_total' => $rating,
            'kategori' => EvaluasiModel::kategoriFromSkor($rating),
            'komentar' => $comment,
            'rekomendasi' => null,
        ];

        if ($existing !== null) {
            $model->update((int) $existing['id'], $data);
        } else {
            $model->insert($data);
        }

        $dpl = model(DplModel::class)->find((int) $detail['dpl_id']);
        if ($dpl && ! empty($dpl['user_id'])) {
            $this->notify((int) $dpl['user_id'], 'Evaluasi mahasiswa diterima', $detail['nama'] . ' telah mengisi evaluasi Anda.', 'success');
        }

        return redirect()->to('/mahasiswa/evaluasi')->with('success', 'Evaluasi berhasil dikirim.');
    }
}
