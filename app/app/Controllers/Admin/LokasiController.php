<?php

namespace App\Controllers\Admin;

use App\Controllers\PanelController;
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
        $this->lokasiModel->insert([
            'nama_desa' => $this->request->getPost('nama_desa'),
            'kecamatan' => $this->request->getPost('kecamatan'),
            'kabupaten' => $this->request->getPost('kabupaten'),
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
        $this->lokasiModel->update($id, [
            'nama_desa' => $this->request->getPost('nama_desa'),
            'kecamatan' => $this->request->getPost('kecamatan'),
            'kabupaten' => $this->request->getPost('kabupaten'),
        ]);

        return redirect()->to('/admin/lokasi')->with('success', 'Lokasi diperbarui.');
    }

    public function delete(int $id)
    {
        $this->lokasiModel->delete($id);

        return redirect()->to('/admin/lokasi')->with('success', 'Lokasi dihapus.');
    }
}
