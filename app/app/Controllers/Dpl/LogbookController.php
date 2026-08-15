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

        $status = $this->request->getGet('status');
        $status = in_array($status, ['menunggu', 'divalidasi', 'ditolak'], true) ? $status : null;

        return $this->render('dpl/logbook/index', [
            'title'    => 'Validasi Logbook',
            'logbooks' => model(LogbookModel::class)->getByDpl($dpl['id'], $status),
            'filterStatus' => $status,
        ]);
    }

    public function proses(int $id)
    {
        $dpl          = model(DplModel::class)->findByUserId((int) current_user()['id']);
        $logbookModel = model(LogbookModel::class);
        $logbook      = $logbookModel->find($id);

        if (! $logbook || ! $dpl) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $mhs = model(MahasiswaModel::class)->getWithRelations((int) $logbook['mahasiswa_id']);

        if (! $mhs || (int) ($mhs['dpl_id'] ?? 0) !== (int) $dpl['id']) {
            return redirect()->back()->with('error', 'Logbook bukan dari mahasiswa bimbingan Anda.');
        }

        if ($logbook['status'] !== 'menunggu') {
            return redirect()->back()->with('error', 'Logbook ini sudah diproses dan tidak dapat diubah dari antrian.');
        }

        $action = $this->request->getPost('action');

        if (! in_array($action, ['validasi', 'tolak'], true)) {
            return redirect()->back()->with('error', 'Aksi validasi tidak dikenali.');
        }

        $status = $action === 'validasi' ? 'divalidasi' : 'ditolak';
        $catatan = trim((string) $this->request->getPost('catatan_dpl'));

        if (mb_strlen($catatan) > 2000) {
            return redirect()->back()->withInput()->with('error', 'Catatan DPL maksimal 2000 karakter.');
        }

        if (! $logbookModel->update($id, [
            'status'       => $status,
            'catatan_dpl'  => $catatan !== '' ? $catatan : null,
            'validated_by' => $dpl['id'],
            'validated_at' => date('Y-m-d H:i:s'),
        ])) {
            return redirect()->back()->with('error', 'Status logbook gagal diperbarui.');
        }

        AuditLib::log(
            $status,
            'logbook',
            'Logbook ' . ($mhs['nama'] ?? '') . ' tanggal ' . ($logbook['tanggal'] ?? '') . ' ' . stempel_label($status),
            $id,
            ['status' => $logbook['status']],
            ['status' => $status, 'catatan_dpl' => $this->request->getPost('catatan_dpl')]
        );

        $this->notifyAdmins(
            'Logbook telah diproses DPL',
            ($mhs['nama'] ?? 'Mahasiswa') . ' memiliki logbook yang ' . stempel_label($status) . ' oleh ' . ($dpl['nama'] ?? 'DPL') . '.',
            $status === 'divalidasi' ? 'success' : 'warning'
        );

        $this->notify(
            (int) $mhs['user_id'],
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
