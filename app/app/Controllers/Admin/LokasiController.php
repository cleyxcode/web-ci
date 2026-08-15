<?php

namespace App\Controllers\Admin;

use App\Controllers\PanelController;
use App\Models\KelompokKknModel;
use App\Models\LokasiKknModel;

class LokasiController extends PanelController
{
    protected LokasiKknModel $lokasiModel;

    public function __construct()
    {
        $this->lokasiModel = model(LokasiKknModel::class);
    }

    public function index()
    {
        return $this->render('admin/lokasi/index', [
            'title'  => 'Lokasi KKN',
            'lokasi' => $this->lokasiModel->findAll(),
        ]);
    }

    public function create()
    {
        return $this->render('admin/lokasi/form', ['title' => 'Tambah Lokasi']);
    }

    public function store()
    {
        if (! $this->validateRules()) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->lokasiModel->insert([
            'nama_desa' => trim((string) $this->request->getPost('nama_desa')),
            'kecamatan' => trim((string) $this->request->getPost('kecamatan')) ?: null,
            'kabupaten' => trim((string) $this->request->getPost('kabupaten')) ?: null,
        ]);

        return redirect()->to('/admin/lokasi')->with('success', 'Lokasi ditambahkan.');
    }

    public function edit(int $id)
    {
        $lokasi = $this->lokasiModel->find($id);

        if (! $lokasi) {
            return redirect()->to('/admin/lokasi')->with('error', 'Data tidak ditemukan.');
        }

        return $this->render('admin/lokasi/form', ['title' => 'Edit Lokasi', 'lokasi' => $lokasi]);
    }

    public function update(int $id)
    {
        if (! $this->lokasiModel->find($id)) {
            return redirect()->to('/admin/lokasi')->with('error', 'Data tidak ditemukan.');
        }

        if (! $this->validateRules()) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->lokasiModel->update($id, [
            'nama_desa' => trim((string) $this->request->getPost('nama_desa')),
            'kecamatan' => trim((string) $this->request->getPost('kecamatan')) ?: null,
            'kabupaten' => trim((string) $this->request->getPost('kabupaten')) ?: null,
        ]);

        return redirect()->to('/admin/lokasi')->with('success', 'Lokasi diperbarui.');
    }

    public function delete(int $id)
    {
        $usedByGroups = model(KelompokKknModel::class)
            ->where('lokasi_id', $id)
            ->countAllResults();

        if ($usedByGroups > 0) {
            return redirect()->to('/admin/lokasi')->with('error', 'Lokasi tidak dapat dihapus karena sudah dipakai kelompok KKN.');
        }

        $this->lokasiModel->delete($id);

        return redirect()->to('/admin/lokasi')->with('success', 'Lokasi dihapus.');
    }

    private function validateRules(): bool
    {
        return $this->validate([
            'nama_desa' => 'required|min_length[3]|max_length[100]',
            'kecamatan' => 'permit_empty|max_length[100]',
            'kabupaten' => 'permit_empty|max_length[100]',
        ]);
    }
}
