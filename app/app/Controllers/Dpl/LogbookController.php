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

    public function edit(int $id)
    {
        $dpl = model(DplModel::class)->findByUserId(current_user()['id']);

        if (! $dpl) {
            return redirect()->to('/dpl/dashboard');
        }

        $logbook = model(LogbookModel::class)->find($id);

        if (! $logbook) {
            return redirect()->to('/dpl/logbook')->with('error', 'Logbook tidak ditemukan.');
        }

        $mhs = model(MahasiswaModel::class)->find((int) $logbook['mahasiswa_id']);
        if (! $mhs || (int) $mhs['dpl_id'] !== (int) $dpl['id']) {
            return redirect()->to('/dpl/logbook')->with('error', 'Logbook bukan dari mahasiswa bimbingan Anda.');
        }

        return $this->render('dpl/logbook/form', [
            'title'   => 'Edit Logbook',
            'logbook' => $logbook,
        ]);
    }

    public function update(int $id)
    {
        $dpl = model(DplModel::class)->findByUserId(current_user()['id']);

        if (! $dpl) {
            return redirect()->to('/dpl/dashboard');
        }

        $logbookModel = model(LogbookModel::class);
        $logbook = $logbookModel->find($id);

        if (! $logbook) {
            return redirect()->to('/dpl/logbook')->with('error', 'Logbook tidak ditemukan.');
        }

        $mhs = model(MahasiswaModel::class)->find((int) $logbook['mahasiswa_id']);
        if (! $mhs || (int) $mhs['dpl_id'] !== (int) $dpl['id']) {
            return redirect()->to('/dpl/logbook')->with('error', 'Logbook bukan dari mahasiswa bimbingan Anda.');
        }

        if (! $this->validate([
            'tanggal'         => 'required|valid_date[Y-m-d]',
            'kegiatan'        => 'required|min_length[5]|max_length[5000]',
            'lokasi_kegiatan' => 'permit_empty|max_length[255]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $tanggal = (string) $this->request->getPost('tanggal');
        if (strtotime($tanggal) > strtotime(date('Y-m-d'))) {
            return redirect()->back()->withInput()->with('error', 'Tanggal kegiatan tidak boleh melebihi hari ini.');
        }

        $dokumentasi = upload_file($this->request->getFile('dokumentasi'), 'logbook', ['jpg', 'jpeg', 'png']);

        $data = [
            'tanggal'         => $this->request->getPost('tanggal'),
            'kegiatan'        => trim((string) $this->request->getPost('kegiatan')),
            'lokasi_kegiatan' => trim((string) $this->request->getPost('lokasi_kegiatan')) ?: null,
        ];

        if ($dokumentasi) {
            $data['dokumentasi'] = $dokumentasi;
        }

        $logbookModel->update($id, $data);

        return redirect()->to('/dpl/logbook')->with('success', 'Logbook berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $dpl = model(DplModel::class)->findByUserId(current_user()['id']);

        if (! $dpl) {
            return redirect()->to('/dpl/dashboard');
        }

        $logbookModel = model(LogbookModel::class);
        $logbook = $logbookModel->find($id);

        if (! $logbook) {
            return redirect()->to('/dpl/logbook')->with('error', 'Logbook tidak ditemukan.');
        }

        $mhs = model(MahasiswaModel::class)->find((int) $logbook['mahasiswa_id']);
        if (! $mhs || (int) $mhs['dpl_id'] !== (int) $dpl['id']) {
            return redirect()->to('/dpl/logbook')->with('error', 'Logbook bukan dari mahasiswa bimbingan Anda.');
        }

        $logbookModel->delete($id);

        return redirect()->to('/dpl/logbook')->with('success', 'Logbook berhasil dihapus.');
    }
}
