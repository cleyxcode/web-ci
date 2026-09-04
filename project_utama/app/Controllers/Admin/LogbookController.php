<?php

namespace App\Controllers\Admin;

use App\Controllers\PanelController;
use App\Models\LogbookModel;

class LogbookController extends PanelController
{
    public function index()
    {
        $logbookModel = model(LogbookModel::class);

        $filterStatus     = $this->request->getGet('status') ?? '';
        $filterSearch     = $this->request->getGet('cari') ?? '';
        $filterDari       = $this->request->getGet('dari') ?? '';
        $filterSampai     = $this->request->getGet('sampai') ?? '';

        $logbooks = $logbookModel->getAllWithFilter(
            $filterStatus !== '' ? $filterStatus : null,
            $filterSearch !== '' ? $filterSearch : null,
            $filterDari !== '' ? $filterDari : null,
            $filterSampai !== '' ? $filterSampai : null,
        );

        return $this->render('admin/logbook/index', [
            'title'         => 'Semua Logbook',
            'logbooks'      => $logbooks,
            'totalSemua'    => $logbookModel->countAll(),
            'totalMenunggu' => $logbookModel->countByStatus('menunggu'),
            'totalValidasi' => $logbookModel->countByStatus('divalidasi'),
            'totalDitolak'  => $logbookModel->countByStatus('ditolak'),
            'filterStatus'  => $filterStatus,
            'filterSearch'  => $filterSearch,
            'filterDari'    => $filterDari,
            'filterSampai'  => $filterSampai,
        ]);
    }

    public function show(int $id)
    {
        $logbook = model(LogbookModel::class)->getDetailById($id);

        if (! $logbook) {
            return redirect()->to('/admin/logbook')->with('error', 'Logbook tidak ditemukan.');
        }

        return $this->render('admin/logbook/show', [
            'title'   => 'Detail Logbook',
            'logbook' => $logbook,
        ]);
    }

    public function delete(int $id)
    {
        $logbookModel = model(LogbookModel::class);
        $logbook      = $logbookModel->find($id);

        if (! $logbook) {
            return redirect()->to('/admin/logbook')->with('error', 'Logbook tidak ditemukan.');
        }

        if (! empty($logbook['dokumentasi'])) {
            $filePath = FCPATH . 'uploads/' . $logbook['dokumentasi'];
            if (is_file($filePath)) {
                @unlink($filePath);
            }
        }

        $logbookModel->delete($id);

        return redirect()->to('/admin/logbook')->with('success', 'Logbook berhasil dihapus.');
    }
}
