<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\PanelController;
use App\Models\EvaluasiKriteriaModel;
use App\Models\EvaluasiModel;
use App\Models\DplModel;
use App\Models\KelompokKknModel;
use App\Models\MahasiswaModel;

final class EvaluasiController extends PanelController
{
    public function index(): string
    {
        $evaluasiModel = model(EvaluasiModel::class);
        $criteriaModel = model(EvaluasiKriteriaModel::class);
        $evaluasi = $evaluasiModel->getAllDplWithMahasiswa();

        return $this->render('admin/evaluasi/index', [
            'title'          => 'Evaluasi DPL',
            'evaluasi'       => $evaluasi,
            'criteria'       => $criteriaModel->getAllOrdered(),
            'kelompok'       => model(KelompokKknModel::class)->getAllWithRelations(),
            'dpl'            => model(DplModel::class)->getAllWithUser(),
            'avgRating'      => $evaluasiModel->averageRating(),
            'totalEvaluasi'  => count($evaluasi),
            'totalMahasiswa' => model(MahasiswaModel::class)->countAllResults(),
        ]);
    }

    public function storeCriteria()
    {
        if (! $this->validateCriteria()) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $criteriaModel = model(EvaluasiKriteriaModel::class);
        $scope = $this->criteriaScope();
        if ($scope === null) {
            return redirect()->back()->withInput()->with('errors', ['cakupan' => 'Pilih target cakupan yang valid.']);
        }
        $criteriaModel->insert([
            'nama'       => trim((string) $this->request->getPost('nama')),
            'deskripsi'  => trim((string) $this->request->getPost('deskripsi')) ?: null,
            'urutan'     => $criteriaModel->nextOrder(),
            'aktif'      => $this->request->getPost('aktif') === '1' ? 1 : 0,
            'cakupan'    => $scope['cakupan'],
            'target_id'  => $scope['target_id'],
            'created_by' => (int) current_user()['id'],
        ]);

        return redirect()->to('/admin/evaluasi')->with('success', 'Kriteria evaluasi berhasil ditambahkan.');
    }

    public function updateCriteria(int $id)
    {
        $criteriaModel = model(EvaluasiKriteriaModel::class);
        if (! $criteriaModel->find($id)) {
            return redirect()->to('/admin/evaluasi')->with('error', 'Kriteria evaluasi tidak ditemukan.');
        }

        if (! $this->validateCriteria()) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $scope = $this->criteriaScope();
        if ($scope === null) {
            return redirect()->back()->withInput()->with('errors', ['cakupan' => 'Pilih target cakupan yang valid.']);
        }

        $order = (int) $this->request->getPost('urutan');
        $criteriaModel->update($id, [
            'nama'      => trim((string) $this->request->getPost('nama')),
            'deskripsi' => trim((string) $this->request->getPost('deskripsi')) ?: null,
            'urutan'    => $order > 0 ? $order : 1,
            'aktif'     => $this->request->getPost('aktif') === '1' ? 1 : 0,
            'cakupan'   => $scope['cakupan'],
            'target_id' => $scope['target_id'],
        ]);

        return redirect()->to('/admin/evaluasi')->with('success', 'Kriteria evaluasi berhasil diperbarui.');
    }

    public function deleteCriteria(int $id)
    {
        $criteriaModel = model(EvaluasiKriteriaModel::class);
        if (! $criteriaModel->find($id)) {
            return redirect()->to('/admin/evaluasi')->with('error', 'Kriteria evaluasi tidak ditemukan.');
        }

        $criteriaModel->delete($id);

        return redirect()->to('/admin/evaluasi')->with('success', 'Kriteria evaluasi dihapus. Riwayat penilaian tetap tersimpan.');
    }

    public function export()
    {
        return redirect()->back()->with('info', 'Fitur export evaluasi akan segera hadir.');
    }

    private function validateCriteria(): bool
    {
        return $this->validate([
            'nama'      => 'required|min_length[3]|max_length[150]',
            'deskripsi' => 'permit_empty|max_length[255]',
            'urutan'    => 'permit_empty|is_natural_no_zero',
        ]);
    }

    /** @return array{cakupan: string, target_id: int|null}|null */
    private function criteriaScope(): ?array
    {
        $cakupan = (string) $this->request->getPost('cakupan');
        $targetId = (int) $this->request->getPost('target_id');

        if ($cakupan === 'semua') {
            return ['cakupan' => 'semua', 'target_id' => null];
        }

        if ($cakupan === 'kelompok' && $targetId > 0 && model(KelompokKknModel::class)->find($targetId)) {
            return ['cakupan' => 'kelompok', 'target_id' => $targetId];
        }

        if ($cakupan === 'dpl' && $targetId > 0 && model(DplModel::class)->find($targetId)) {
            return ['cakupan' => 'dpl', 'target_id' => $targetId];
        }

        return null;
    }
}
