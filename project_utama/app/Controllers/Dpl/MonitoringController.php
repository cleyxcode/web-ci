<?php

namespace App\Controllers\Dpl;

use App\Controllers\PanelController;
use App\Models\DplModel;
use App\Models\KelompokKknModel;
use App\Models\LogbookModel;

class MonitoringController extends PanelController
{
    public function index()
    {
        $dpl = model(DplModel::class)->findByUserId(current_user()['id']);

        if (! $dpl) {
            return redirect()->to('/dpl/dashboard')->with('error', 'Profil DPL tidak ditemukan.');
        }

        $status = $this->request->getGet('status');
        $status = in_array($status, ['menunggu', 'divalidasi', 'ditolak'], true) ? $status : null;

        return $this->render('dpl/monitoring', [
            'title'        => 'Monitoring Kegiatan',
            'logbooks'     => model(LogbookModel::class)->getByDpl($dpl['id'], $status),
            'petaKelompok' => model(KelompokKknModel::class)->getWithGps($dpl['id']),
            'filterStatus' => $status,
        ]);
    }
}
