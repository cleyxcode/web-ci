<?php

namespace App\Controllers\Dpl;

use App\Controllers\PanelController;
use App\Libraries\AuditLib;
use App\Models\DplModel;
use App\Models\LaporanModel;
use App\Models\MahasiswaModel;

class LaporanController extends PanelController
{
    public function index()
    {
        $dpl = model(DplModel::class)->findByUserId(current_user()['id']);

        if (! $dpl) {
            return redirect()->to('/dpl/dashboard');
        }

        return $this->render('dpl/laporan/index', [
            'title'   => 'Review Laporan',
            'laporan' => model(LaporanModel::class)->getByDpl($dpl['id']),
        ]);
    }

    public function review(int $id)
    {
        $dpl          = model(DplModel::class)->findByUserId((int) current_user()['id']);
        $laporanModel = model(LaporanModel::class);
        $laporan      = $laporanModel->find($id);

        if (! $laporan || ! $dpl) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $mhs = model(MahasiswaModel::class)->getWithRelations((int) $laporan['mahasiswa_id']);

        if (! $mhs || (int) ($mhs['dpl_id'] ?? 0) !== (int) $dpl['id']) {
            return redirect()->back()->with('error', 'Laporan bukan dari mahasiswa bimbingan Anda.');
        }

        $action = $this->request->getPost('action');
        $status = $action === 'terima' ? 'diterima' : 'ditolak';

        $laporanModel->update($id, [
            'status'      => $status,
            'catatan_dpl' => $this->request->getPost('catatan_dpl'),
            'reviewed_by' => $dpl['id'],
            'reviewed_at' => date('Y-m-d H:i:s'),
        ]);

        AuditLib::log(
            $status,
            'laporan',
            'Laporan "' . ($laporan['judul'] ?? '') . '" ' . stempel_label($status),
            $id,
            ['status' => $laporan['status']],
            ['status' => $status, 'catatan_dpl' => $this->request->getPost('catatan_dpl')]
        );

        $this->notify(
            (int) $mhs['user_id'],
            'Laporan ' . stempel_label($status),
            'Laporan "' . $laporan['judul'] . '" ' . stempel_label($status),
            $status === 'diterima' ? 'success' : 'danger',
            'laporan.reviewed',
            ['laporan_id' => $id, 'status' => $status]
        );

        $this->pusher->trigger('kkn-channel', 'laporan.reviewed', [
            'laporan_id'     => $id,
            'status'         => $status,
            'nama_mahasiswa' => $mhs['nama'],
        ]);

        return redirect()->back()->with('success', 'Laporan berhasil direview.');
    }
}
