<?php

namespace App\Controllers\Admin;

use App\Controllers\PanelController;
use App\Libraries\AuditLib;
use App\Models\LaporanModel;
use Throwable;

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

        if ($laporan === null) {
            return redirect()->to('/admin/laporan')->with('error', 'Laporan tidak ditemukan.');
        }

        try {
            foreach (stored_files($laporan['file_laporan'] ?? null) as $oldFile) {
                delete_uploaded_file($oldFile);
            }

            $laporanModel->delete($id);

            AuditLib::log(
                'hapus',
                'laporan',
                'Admin menghapus laporan "' . ($laporan['judul'] ?? '') . '"',
                $id,
                $laporan,
                null
            );
        } catch (Throwable $e) {
            log_message('error', 'Gagal hapus laporan admin #{id}: {message}', [
                'id'      => $id,
                'message' => $e->getMessage(),
            ]);

            return redirect()->to('/admin/laporan')->with('error', 'Gagal menghapus laporan. Silakan coba lagi.');
        }

        return redirect()->to('/admin/laporan')->with('success', 'Laporan berhasil dihapus.');
    }
}
