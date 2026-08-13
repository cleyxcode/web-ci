<?php

namespace App\Controllers\Admin;

use App\Controllers\PanelController;
use App\Models\AuditTrailModel;

class AuditController extends PanelController
{
    public function index()
    {
        $aksi    = $this->request->getGet('aksi');
        $entitas = $this->request->getGet('entitas');
        $model   = model(AuditTrailModel::class);

        if (is_string($aksi) && $aksi !== '') {
            $model->where('aksi', $aksi);
        }

        if (is_string($entitas) && $entitas !== '') {
            $model->where('entitas', $entitas);
        }

        return $this->render('admin/audit/index', [
            'title'   => 'Audit Trail',
            'logs'    => $model->orderBy('created_at', 'DESC')->findAll(200),
            'aksi'    => $aksi,
            'entitas' => $entitas,
        ]);
    }
}
