<?php

namespace App\Controllers\Dpl;

use App\Controllers\PanelController;
use App\Libraries\AuditLib;
use App\Models\DplModel;
use App\Models\LogbookModel;
use App\Models\MahasiswaModel;

class LogbookController extends PanelController
{
    public function index()
    {
        $dpl = model(DplModel::class)->findByUserId(current_user()['id']);

        if (! $dpl) {
            return redirect()->to('/dpl/dashboard');
        }

        return $this->render('dpl/logbook/index', [
            'title'    => 'Validasi Logbook',
            'logbooks' => model(LogbookModel::class)->getByDpl($dpl['id']),
        ]);
    }

    public function proses(int $id)
    {
        $dpl        = model(DplModel::class)->findByUserId(current_user()['id']);
        $logbookModel = model(LogbookModel::class);
        $logbook    = $logbookModel->find($id);

        if (! $logbook || ! $dpl) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $action = $this->request->getPost('action');
        $status = $action === 'validasi' ? 'divalidasi' : 'ditolak';

        $logbookModel->update($id, [
            'status'       => $status,
            'catatan_dpl'  => $this->request->getPost('catatan_dpl'),
            'validated_by' => $dpl['id'],
            'validated_at' => date('Y-m-d H:i:s'),
        ]);

        $mhs = model(MahasiswaModel::class)->find($logbook['mahasiswa_id']);

        AuditLib::log(
            $status,
            'logbook',
            'Logbook ' . ($mhs['nama'] ?? '') . ' tanggal ' . ($logbook['tanggal'] ?? '') . ' ' . stempel_label($status),
            $id,
            ['status' => $logbook['status']],
            ['status' => $status, 'catatan_dpl' => $this->request->getPost('catatan_dpl')]
        );

        $this->notify(
            $mhs['user_id'],
            'Logbook ' . stempel_label($status),
            'Logbook tanggal ' . format_tanggal($logbook['tanggal']) . ' ' . stempel_label($status),
            $status === 'divalidasi' ? 'success' : 'danger',
            'logbook.validated',
            ['logbook_id' => $id, 'status' => $status]
        );

        $this->pusher->trigger('kkn-channel', 'logbook.validated', [
            'logbook_id'     => $id,
            'status'         => $status,
            'nama_mahasiswa' => $mhs['nama'],
        ]);

        return redirect()->back()->with('success', 'Logbook berhasil ' . stempel_label($status) . '.');
    }
}
