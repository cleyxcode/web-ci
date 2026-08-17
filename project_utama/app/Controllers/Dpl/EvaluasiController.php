<?php

declare(strict_types=1);

namespace App\Controllers\Dpl;

use App\Controllers\PanelController;
use App\Libraries\AuditLib;
use App\Models\DplModel;
use App\Models\EvaluasiKriteriaModel;
use App\Models\EvaluasiModel;
use App\Models\MahasiswaModel;
use App\Models\KelompokKknModel;

final class EvaluasiController extends PanelController
{
    public function index(): string
    {
        $dpl = $this->currentDpl();
        if ($dpl === null) {
            return $this->render('dpl/evaluasi/index', [
                'title'          => 'Evaluasi Mahasiswa',
                'evaluasi'       => [],
                'belumEvaluasi'  => [],
                'avgRating'      => null,
                'totalEvaluasi'  => 0,
                'totalMahasiswa' => 0,
            ]);
        }

        $evaluasiModel = model(EvaluasiModel::class);
        $mahasiswa = model(MahasiswaModel::class)->getByDplId((int) $dpl['id']);
        $evaluasi = $evaluasiModel->getByDpl((int) $dpl['id']);
        $submittedIds = array_map('intval', array_column($evaluasi, 'mahasiswa_id'));

        $belumEvaluasi = array_values(array_filter(
            $mahasiswa,
            static fn (array $row): bool => ! in_array((int) $row['id'], $submittedIds, true)
        ));

        return $this->render('dpl/evaluasi/index', [
            'title'          => 'Evaluasi Mahasiswa',
            'evaluasi'       => $evaluasi,
            'belumEvaluasi'  => $belumEvaluasi,
            'avgRating'      => $evaluasiModel->averageRating((int) $dpl['id']),
            'totalEvaluasi'  => count($evaluasi),
            'totalMahasiswa' => count($mahasiswa),
            'kelompok'       => model(KelompokKknModel::class)->getByDplId((int) $dpl['id']),
            'criteria'       => array_values(array_filter(
                model(EvaluasiKriteriaModel::class)->getActiveOrdered(),
                static fn (array $row): bool => ($row['cakupan'] ?? 'semua') === 'semua'
                    || (int) ($row['target_id'] ?? 0) === (int) $dpl['id']
            )),
        ]);
    }

    public function form(int $mahasiswaId): string
    {
        $context = $this->studentContext($mahasiswaId);
        if ($context === null) {
            return $this->render('dpl/evaluasi/index', [
                'title'          => 'Evaluasi Mahasiswa',
                'evaluasi'       => [],
                'belumEvaluasi'  => [],
                'avgRating'      => null,
                'totalEvaluasi'  => 0,
                'totalMahasiswa' => 0,
            ]);
        }

        return $this->render('dpl/evaluasi/form', [
            'title'    => 'Evaluasi Mahasiswa',
            'mahasiswa'=> $context['mahasiswa'],
            'evaluasi' => $context['evaluasi'],
            'criteria' => model(EvaluasiKriteriaModel::class)->getForDpl(
                (int) $context['dpl']['id'],
                (int) $context['mahasiswa']['kelompok_id']
            ),
        ]);
    }

    public function save(int $mahasiswaId)
    {
        $context = $this->studentContext($mahasiswaId);
        if ($context === null) {
            return redirect()->to('/dpl/evaluasi')->with('error', 'Mahasiswa bukan bagian dari kelompok bimbingan Anda.');
        }

        $criteria = model(EvaluasiKriteriaModel::class)->getForDpl(
            (int) $context['dpl']['id'],
            (int) $context['mahasiswa']['kelompok_id']
        );
        if ($criteria === []) {
            return redirect()->to('/dpl/evaluasi/' . $mahasiswaId)
                ->with('error', 'Admin belum mengatur kriteria evaluasi.');
        }

        $ratings = $this->request->getPost('criteria_rating');
        $errors = [];
        $detail = [];
        $total = 0;

        if (! is_array($ratings)) {
            $ratings = [];
        }

        foreach ($criteria as $criterion) {
            $criterionId = (int) $criterion['id'];
            $rating = (int) ($ratings[$criterionId] ?? 0);

            if ($rating < 1 || $rating > 5) {
                $errors['criteria_rating_' . $criterionId] = 'Beri rating 1–5 untuk: ' . $criterion['nama'];
                continue;
            }

            $detail[] = [
                'id'        => $criterionId,
                'nama'      => (string) $criterion['nama'],
                'deskripsi' => (string) ($criterion['deskripsi'] ?? ''),
                'rating'    => $rating,
            ];
            $total += $rating;
        }

        $comment = trim((string) $this->request->getPost('komentar'));
        if (mb_strlen($comment) > 2000) {
            $errors['komentar'] = 'Catatan maksimal 2.000 karakter.';
        }

        if ($errors !== []) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $rating = round($total / count($detail), 2);
        $existing = model(EvaluasiModel::class)->findByMahasiswaDpl($mahasiswaId);
        $data = [
            'mahasiswa_id'      => $mahasiswaId,
            'tipe_evaluasi'     => 'dpl',
            'kelompok_id'       => (int) $context['mahasiswa']['kelompok_id'],
            'dpl_id'            => (int) $context['dpl']['id'],
            'penilai_id'        => (int) current_user()['id'],
            'detail_evaluasi'   => json_encode($detail, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'rating'            => $rating,
            'aspek_bimbingan'   => (int) round($rating),
            'aspek_lokasi'      => (int) round($rating),
            'aspek_pelaksanaan' => (int) round($rating),
            'skor_total'        => $rating,
            'kategori'          => EvaluasiModel::kategoriFromSkor((float) $rating),
            'komentar'          => $comment,
            'rekomendasi'       => $comment,
        ];

        $evaluasiModel = model(EvaluasiModel::class);
        $isUpdate = $existing !== null;
        $evaluationId = $isUpdate
            ? (int) $existing['id']
            : (int) $evaluasiModel->insert($data);

        if ($isUpdate) {
            $evaluasiModel->update($evaluationId, $data);
        }

        AuditLib::log(
            $isUpdate ? 'update_evaluasi_dpl' : 'submit_evaluasi_dpl',
            'evaluasi',
            ($context['mahasiswa']['nama'] ?? 'Mahasiswa') . ' menerima evaluasi DPL (' . $rating . '/5)',
            $evaluationId,
            $isUpdate ? ['rating' => $existing['rating'] ?? null] : null,
            ['rating' => $rating, 'kriteria' => count($detail)]
        );

        $this->notify(
            (int) $context['mahasiswa']['user_id'],
            $isUpdate ? 'Evaluasi DPL diperbarui' : 'Evaluasi DPL tersedia',
            'DPL Anda telah mengisi evaluasi. Buka menu Evaluasi untuk melihat rincian penilaian.',
            'info'
        );

        $this->notifyAdmins(
            $isUpdate ? 'Evaluasi DPL diperbarui' : 'Evaluasi DPL baru',
            ($context['mahasiswa']['nama'] ?? 'Mahasiswa') . ' menerima evaluasi dari DPL.',
            'info'
        );

        return redirect()->to('/dpl/evaluasi')->with('success', 'Evaluasi DPL berhasil disimpan.');
    }

    public function groupForm(int $kelompokId): string
    {
        $dpl = $this->currentDpl();
        $kelompok = $dpl ? model(KelompokKknModel::class)->where('id', $kelompokId)->where('dpl_id', $dpl['id'])->first() : null;
        if (! $kelompok) {
            return redirect()->to('/dpl/evaluasi')->with('error', 'Kelompok bukan bagian dari bimbingan Anda.');
        }

        $students = model(MahasiswaModel::class)->getByKelompokId($kelompokId);
        $evaluasi = [];
        foreach ($students as $student) {
            $evaluasi[(int) $student['id']] = model(EvaluasiModel::class)->findByMahasiswaDpl((int) $student['id']);
        }

        return $this->render('dpl/evaluasi/group-form', [
            'title' => 'Evaluasi Satu Kelompok', 'kelompok' => $kelompok, 'mahasiswa' => $students,
            'evaluasi' => $evaluasi,
            'criteria' => model(EvaluasiKriteriaModel::class)->getForDpl((int) $dpl['id'], $kelompokId),
        ]);
    }

    public function saveGroup(int $kelompokId)
    {
        $dpl = $this->currentDpl();
        $kelompok = $dpl ? model(KelompokKknModel::class)->where('id', $kelompokId)->where('dpl_id', $dpl['id'])->first() : null;
        if (! $kelompok) {
            return redirect()->to('/dpl/evaluasi')->with('error', 'Kelompok bukan bagian dari bimbingan Anda.');
        }

        $criteria = model(EvaluasiKriteriaModel::class)->getForDpl((int) $dpl['id'], $kelompokId);
        $students = model(MahasiswaModel::class)->getByKelompokId($kelompokId);
        $ratings = $this->request->getPost('ratings');
        $comments = $this->request->getPost('komentar');
        $model = model(EvaluasiModel::class);
        $errors = [];

        foreach ($students as $student) {
            $studentId = (int) $student['id'];
            foreach ($criteria as $criterion) {
                $value = (int) ($ratings[$studentId][$criterion['id']] ?? 0);
                if ($value < 1 || $value > 5) {
                    $errors['rating_' . $studentId . '_' . $criterion['id']] = 'Lengkapi semua rating untuk ' . $student['nama'] . '.';
                }
            }
        }
        if ($errors !== []) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        foreach ($students as $student) {
            $studentId = (int) $student['id']; $detail = []; $total = 0;
            foreach ($criteria as $criterion) {
                $value = (int) $ratings[$studentId][$criterion['id']]; $total += $value;
                $detail[] = ['id' => (int) $criterion['id'], 'nama' => $criterion['nama'], 'deskripsi' => $criterion['deskripsi'] ?? '', 'rating' => $value];
            }
            $score = round($total / max(count($detail), 1), 2);
            $data = ['mahasiswa_id' => $studentId, 'tipe_evaluasi' => 'dpl', 'kelompok_id' => $kelompokId, 'dpl_id' => (int) $dpl['id'], 'penilai_id' => (int) current_user()['id'], 'detail_evaluasi' => json_encode($detail, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 'rating' => $score, 'aspek_bimbingan' => round($score), 'aspek_lokasi' => round($score), 'aspek_pelaksanaan' => round($score), 'skor_total' => $score, 'kategori' => EvaluasiModel::kategoriFromSkor((float) $score), 'komentar' => trim((string) ($comments[$studentId] ?? '')), 'rekomendasi' => trim((string) ($comments[$studentId] ?? ''))];
            $existing = $model->findByMahasiswaDpl($studentId);
            $existing ? $model->update((int) $existing['id'], $data) : $model->insert($data);
        }

        $this->notifyAdmins('Evaluasi kelompok baru', 'DPL ' . $dpl['nama'] . ' mengirim evaluasi untuk kelompok ' . ($kelompok['nama_kelompok'] ?? '-') . '.', 'info');
        return redirect()->to('/dpl/evaluasi')->with('success', 'Evaluasi seluruh anggota kelompok berhasil disimpan.');
    }

    public function storeCriteria()
    {
        $dpl = $this->currentDpl();
        if (! $dpl) return redirect()->to('/dpl/evaluasi')->with('error', 'Profil DPL tidak ditemukan.');
        if (! $this->validate(['nama' => 'required|min_length[3]|max_length[150]', 'deskripsi' => 'permit_empty|max_length[255]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $model = model(EvaluasiKriteriaModel::class);
        $model->insert(['nama' => trim((string) $this->request->getPost('nama')), 'deskripsi' => trim((string) $this->request->getPost('deskripsi')) ?: null, 'urutan' => $model->nextOrder(), 'aktif' => 1, 'cakupan' => 'dpl', 'target_id' => (int) $dpl['id'], 'created_by' => (int) current_user()['id']]);
        return redirect()->to('/dpl/evaluasi')->with('success', 'Pertanyaan evaluasi baru berhasil ditambahkan.');
    }

    public function updateCriteria(int $id)
    {
        $dpl = $this->currentDpl(); $model = model(EvaluasiKriteriaModel::class); $criterion = $model->find($id);
        if (! $dpl || ! $criterion) return redirect()->to('/dpl/evaluasi')->with('error', 'Pertanyaan evaluasi tidak ditemukan.');
        if (! $this->validate(['nama' => 'required|min_length[3]|max_length[150]', 'deskripsi' => 'permit_empty|max_length[255]'])) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        // DPL boleh menyesuaikan pertanyaan global dan pertanyaan miliknya sendiri.
        if (($criterion['cakupan'] ?? 'semua') === 'dpl' && (int) ($criterion['target_id'] ?? 0) !== (int) $dpl['id']) return redirect()->to('/dpl/evaluasi')->with('error', 'Pertanyaan ini milik DPL lain.');
        $model->update($id, ['nama' => trim((string) $this->request->getPost('nama')), 'deskripsi' => trim((string) $this->request->getPost('deskripsi')) ?: null]);
        return redirect()->to('/dpl/evaluasi')->with('success', 'Pertanyaan evaluasi berhasil diperbarui.');
    }

    public function deleteCriteria(int $id)
    {
        $dpl = $this->currentDpl(); $model = model(EvaluasiKriteriaModel::class); $criterion = $model->find($id);
        if (! $dpl || ! $criterion) return redirect()->to('/dpl/evaluasi')->with('error', 'Pertanyaan evaluasi tidak ditemukan.');
        if (($criterion['cakupan'] ?? 'semua') !== 'dpl' || (int) ($criterion['target_id'] ?? 0) !== (int) $dpl['id']) return redirect()->to('/dpl/evaluasi')->with('error', 'Pertanyaan bawaan global tidak dapat dihapus, hanya dapat disesuaikan.');
        $model->delete($id);
        return redirect()->to('/dpl/evaluasi')->with('success', 'Pertanyaan tambahan dihapus.');
    }

    /** @return array<string, array<string, mixed>>|null */
    private function studentContext(int $mahasiswaId): ?array
    {
        $dpl = $this->currentDpl();
        $mahasiswa = model(MahasiswaModel::class)->getWithRelations($mahasiswaId);

        if ($dpl === null || $mahasiswa === null || (int) ($mahasiswa['dpl_id'] ?? 0) !== (int) $dpl['id']) {
            return null;
        }

        return [
            'dpl'       => $dpl,
            'mahasiswa' => $mahasiswa,
            'evaluasi'  => model(EvaluasiModel::class)->findByMahasiswaDpl($mahasiswaId),
        ];
    }

    /** @return array<string, mixed>|null */
    private function currentDpl(): ?array
    {
        return model(DplModel::class)->findByUserId((int) current_user()['id']);
    }
}
