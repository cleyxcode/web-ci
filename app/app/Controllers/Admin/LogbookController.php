<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\PanelController;
use App\Libraries\AuditLib;
use App\Models\LogbookModel;

final class LogbookController extends PanelController
{
    public function index()
    {
        $status = $this->request->getGet('status');
        $status = in_array($status, ['menunggu', 'divalidasi', 'ditolak'], true) ? $status : null;
        $model  = model(LogbookModel::class);
        $builder = $model->select('logbook.*, mahasiswa.nama as nama_mahasiswa, mahasiswa.npm,
                kelompok_kkn.nama_kelompok, dpl.nama as nama_dpl,
                lokasi_kkn.nama_desa, lokasi_kkn.kecamatan, lokasi_kkn.kabupaten')
            ->join('mahasiswa', 'mahasiswa.id = logbook.mahasiswa_id')
            ->join('kelompok_kkn', 'kelompok_kkn.id = mahasiswa.kelompok_id', 'left')
            ->join('dpl', 'dpl.id = kelompok_kkn.dpl_id', 'left')
            ->join('lokasi_kkn', 'lokasi_kkn.id = kelompok_kkn.lokasi_id', 'left');
        if ($status !== null) {
            $builder->where('logbook.status', $status);
        }

        $logbooks = $builder->orderBy('logbook.tanggal', 'DESC')->findAll();

        return $this->render('admin/logbook/index', [
            'title'        => 'Logbook Mahasiswa',
            'logbooks'     => $logbooks,
            'filterStatus' => $status,
            'total'        => count($logbooks),
        ]);
    }

    public function delete(int $id)
    {
        $logbookModel = model(LogbookModel::class);
        $logbook      = $logbookModel->find($id);

        if ($logbook === null) {
            return redirect()->to('/admin/logbook')->with('error', 'Logbook tidak ditemukan.');
        }

        try {
            foreach (stored_files($logbook['dokumentasi'] ?? null) as $oldFile) {
                delete_uploaded_file($oldFile);
            }

            $logbookModel->delete($id);

            AuditLib::log(
                'hapus',
                'logbook',
                'Admin menghapus logbook #' . $id . ' tanggal ' . ($logbook['tanggal'] ?? ''),
                $id,
                $logbook,
                null
            );
        } catch (\Throwable $e) {
            log_message('error', 'Gagal hapus logbook admin #{id}: {message}', [
                'id'      => $id,
                'message' => $e->getMessage(),
            ]);

            return redirect()->to('/admin/logbook')->with('error', 'Gagal menghapus logbook. Silakan coba lagi.');
        }

        return redirect()->to('/admin/logbook')->with('success', 'Logbook berhasil dihapus.');
    }
}
