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

        $status = $this->request->getGet('status');
        $status = in_array($status, ['menunggu', 'diterima', 'ditolak'], true) ? $status : null;

        return $this->render('dpl/laporan/index', [
            'title'   => 'Review Laporan',
            'laporan' => model(LaporanModel::class)->getByDpl($dpl['id'], $status),
            'filterStatus' => $status,
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

        if ($laporan['status'] !== 'menunggu') {
            return redirect()->back()->with('error', 'Laporan ini sudah direview dan tidak dapat diproses ulang dari antrian.');
        }

        $action = $this->request->getPost('action');

        if (! in_array($action, ['terima', 'tolak'], true)) {
            return redirect()->back()->with('error', 'Aksi review tidak dikenali.');
        }

        $status = $action === 'terima' ? 'diterima' : 'ditolak';
        $catatan = trim((string) $this->request->getPost('catatan_dpl'));

        if (mb_strlen($catatan) > 2000) {
            return redirect()->back()->withInput()->with('error', 'Catatan DPL maksimal 2000 karakter.');
        }

        if (! $laporanModel->update($id, [
            'status'      => $status,
            'catatan_dpl' => $catatan !== '' ? $catatan : null,
            'reviewed_by' => $dpl['id'],
            'reviewed_at' => date('Y-m-d H:i:s'),
        ])) {
            return redirect()->back()->with('error', 'Status laporan gagal diperbarui.');
        }

        AuditLib::log(
            $status,
            'laporan',
            'Laporan "' . ($laporan['judul'] ?? '') . '" ' . stempel_label($status),
            $id,
            ['status' => $laporan['status']],
            ['status' => $status, 'catatan_dpl' => $this->request->getPost('catatan_dpl')]
        );

        $this->notifyAdmins(
            'Laporan telah direview DPL',
            'Laporan "' . ($laporan['judul'] ?? 'Laporan') . '" milik ' . ($mhs['nama'] ?? 'Mahasiswa') . ' telah ' . stempel_label($status) . '.',
            $status === 'diterima' ? 'success' : 'warning'
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
