<?php

namespace App\Controllers\Admin;

use App\Controllers\PanelController;
use App\Models\LaporanModel;

class LaporanController extends PanelController
{
    public function index()
    {
        return $this->render('admin/laporan/index', [
            'title'   => 'Semua Laporan',
            'laporan' => model(LaporanModel::class)->getAllWithMahasiswa(),
        ]);
    }

    public function delete(int $id)
    {
        $laporanModel = model(LaporanModel::class);
        $laporan      = $laporanModel->find($id);

        if (! $laporan) {
            return redirect()->to('/admin/laporan')->with('error', 'Laporan tidak ditemukan.');
        }

        // Hapus file PDF jika ada
        if (! empty($laporan['file_laporan'])) {
            $filePath = FCPATH . 'uploads/' . $laporan['file_laporan'];
            if (is_file($filePath)) {
                @unlink($filePath);
            }
        }

        $laporanModel->delete($id);

        return redirect()->to('/admin/laporan')->with('success', 'Laporan berhasil dihapus.');
    }
}
