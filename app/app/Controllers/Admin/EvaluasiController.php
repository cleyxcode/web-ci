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

    public function create()
    {
        return $this->render('admin/evaluasi/form', [
            'title'      => 'Buat Evaluasi Mahasiswa',
            'mahasiswa'  => model(MahasiswaModel::class)->getAllWithRelations(),
            'evaluasi'   => null,
            'isEdit'     => false,
        ]);
    }

    public function store()
    {
        if (! $this->validateEvaluation()) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $mahasiswaId = (int) $this->request->getPost('mahasiswa_id');
        $model       = model(EvaluasiModel::class);
        $existing    = $model->findByMahasiswaAndType($mahasiswaId, 'admin');

        if ($existing) {
            return redirect()->to('/admin/evaluasi/' . $existing['id'] . '/edit')
                ->with('warning', 'Evaluasi admin untuk mahasiswa ini sudah ada. Silakan perbarui evaluasi tersebut.');
        }

        $mahasiswa = model(MahasiswaModel::class)->find($mahasiswaId);
        if (! $mahasiswa) {
            return redirect()->back()->withInput()->with('error', 'Mahasiswa tidak ditemukan.');
        }

        $evaluasiId = (int) $model->insert($this->evaluationData($mahasiswa));
        $this->notify((int) $mahasiswa['user_id'], 'Evaluasi dari admin tersedia', 'Admin telah mengirim evaluasi untuk Anda. Silakan buka menu Evaluasi.', 'info');

        return redirect()->to('/admin/evaluasi')->with('success', 'Evaluasi mahasiswa berhasil dibuat.');
    }

    public function edit(int $id)
    {
        $evaluasi = model(EvaluasiModel::class)->find($id);

        if (! $evaluasi || ($evaluasi['tipe_evaluasi'] ?? '') !== 'admin') {
            return redirect()->to('/admin/evaluasi')->with('error', 'Evaluasi admin tidak ditemukan.');
        }

        return $this->render('admin/evaluasi/form', [
            'title'      => 'Perbarui Evaluasi Mahasiswa',
            'mahasiswa'  => model(MahasiswaModel::class)->getAllWithRelations(),
            'evaluasi'   => $evaluasi,
            'isEdit'     => true,
        ]);
    }

    public function update(int $id)
    {
        $model    = model(EvaluasiModel::class);
        $evaluasi = $model->find($id);

        if (! $evaluasi || ($evaluasi['tipe_evaluasi'] ?? '') !== 'admin') {
            return redirect()->to('/admin/evaluasi')->with('error', 'Evaluasi admin tidak ditemukan.');
        }

        if (! $this->validateEvaluation()) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $mahasiswa = model(MahasiswaModel::class)->find((int) $evaluasi['mahasiswa_id']);
        if (! $mahasiswa) {
            return redirect()->to('/admin/evaluasi')->with('error', 'Mahasiswa tidak ditemukan.');
        }

        $model->update($id, $this->evaluationData($mahasiswa));
        $this->notify((int) $mahasiswa['user_id'], 'Evaluasi admin diperbarui', 'Admin memperbarui evaluasi untuk Anda. Silakan buka menu Evaluasi.', 'info');

        return redirect()->to('/admin/evaluasi')->with('success', 'Evaluasi mahasiswa berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $model = model(EvaluasiModel::class);
        $evaluasi = $model->find($id);

        if (! $evaluasi) {
            return redirect()->back()->with('error', 'Evaluasi tidak ditemukan.');
        }

        $model->delete($id);

        return redirect()->back()->with('success', 'Evaluasi berhasil dihapus.');
    }

    public function export()
    {
        // Placeholder untuk export
        return redirect()->back()->with('info', 'Fitur export segera hadir.');
    }

    private function validateEvaluation(): bool
    {
        if (! $this->validate([
            'mahasiswa_id' => 'required|is_natural_no_zero',
            'rating'       => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[5]',
            'komentar'     => 'required|min_length[3]|max_length[2000]',
        ])) {
            return false;
        }

        $criteria = $this->criteriaFromRequest();
        if ($criteria === []) {
            $this->validator->setError('criteria_nama', 'Tambahkan minimal satu kriteria dan ratingnya.');
            return false;
        }

        return true;
    }

    /**
     * @param array<string, mixed> $mahasiswa
     * @return array<string, int|float|string|null>
     */
    private function evaluationData(array $mahasiswa): array
    {
        $rating     = (int) $this->request->getPost('rating');
        $kelompokId = (int) ($mahasiswa['kelompok_id'] ?? 0);
        $dplId      = null;

        if ($kelompokId > 0) {
            $kelompok = model(\App\Models\KelompokKknModel::class)->find($kelompokId);
            $dplId    = ! empty($kelompok['dpl_id']) ? (int) $kelompok['dpl_id'] : null;
        }

        $komentar = trim((string) $this->request->getPost('komentar'));
        $criteria = $this->criteriaFromRequest();

        return [
            'mahasiswa_id'      => (int) $mahasiswa['id'],
            'tipe_evaluasi'     => 'admin',
            'kelompok_id'       => $kelompokId ?: null,
            'dpl_id'            => $dplId,
            'penilai_id'        => (int) current_user()['id'],
            'detail_evaluasi'   => json_encode($criteria, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'rating'            => $rating,
            'aspek_bimbingan'   => $rating,
            'aspek_lokasi'      => $rating,
            'aspek_pelaksanaan' => $rating,
            'skor_total'        => (float) $rating,
            'kategori'          => $this->kategoriFromRating($rating),
            'komentar'          => $komentar,
            'rekomendasi'       => $komentar,
        ];
    }

    private function kategoriFromRating(int $rating): string
    {
        return match ($rating) {
            5 => 'Sangat Baik',
            4 => 'Baik',
            3 => 'Cukup',
            2 => 'Perlu Perbaikan',
            default => 'Perlu Revisi',
        };
    }

    /**
     * @return list<array{nama: string, rating: int}>
     */
    private function criteriaFromRequest(): array
    {
        $names   = $this->request->getPost('criteria_nama');
        $ratings = $this->request->getPost('criteria_rating');

        if (! is_array($names) || ! is_array($ratings)) {
            return [];
        }

        $criteria = [];
        foreach ($names as $index => $name) {
            $nama   = trim((string) $name);
            $rating = (int) ($ratings[$index] ?? 0);

            if ($nama === '' || $rating < 1 || $rating > 5) {
                continue;
            }

            $criteria[] = ['nama' => mb_substr($nama, 0, 100), 'rating' => $rating];
        }

        return $criteria;
    }
}
