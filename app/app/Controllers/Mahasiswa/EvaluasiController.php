<?php

declare(strict_types=1);

namespace App\Controllers\Mahasiswa;

use App\Controllers\PanelController;
use App\Libraries\AuditLib;
use App\Models\EvaluasiModel;
use App\Models\MahasiswaModel;

class EvaluasiController extends PanelController
{
    public function index()
    {
        $mhs = model(MahasiswaModel::class)->findByUserId((int) current_user()['id']);

        if (! $mhs) {
            return redirect()->to('/mahasiswa/dashboard')->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        $detail = model(MahasiswaModel::class)->getWithRelations((int) $mhs['id']);

        return $this->render('mahasiswa/evaluasi/index', [
            'title'         => 'Evaluasi Kegiatan',
            'mahasiswa'     => $detail,
            'evaluasi'      => model(EvaluasiModel::class)->findByMahasiswa((int) $mhs['id']),
            'evaluasiAdmin' => model(EvaluasiModel::class)->findByMahasiswaAndType((int) $mhs['id'], 'admin'),
        ]);
    }

    public function store()
    {
        $mhs = model(MahasiswaModel::class)->findByUserId((int) current_user()['id']);

        if (! $mhs) {
            return redirect()->to('/mahasiswa/dashboard');
        }

        if (empty($mhs['kelompok_id'])) {
            return redirect()->to('/mahasiswa/evaluasi')
                ->with('error', 'Anda belum ditempatkan di kelompok KKN. Evaluasi belum dapat dikirim.');
        }

        $rules = [
            'rating'             => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[5]',
            'aspek_bimbingan'    => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[5]',
            'aspek_lokasi'       => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[5]',
            'aspek_pelaksanaan'  => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[5]',
            'komentar'           => 'permit_empty|max_length[2000]',
            'kategori'           => 'required|max_length[100]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $rating       = (int) $this->request->getPost('rating');
        $bimbingan    = (int) $this->request->getPost('aspek_bimbingan');
        $lokasi       = (int) $this->request->getPost('aspek_lokasi');
        $pelaksanaan  = (int) $this->request->getPost('aspek_pelaksanaan');
        $skorTotal    = EvaluasiModel::hitungSkor($rating, $bimbingan, $lokasi, $pelaksanaan);
        $kategori     = trim((string) $this->request->getPost('kategori'));
        if (empty($kategori)) {
            $kategori = EvaluasiModel::kategoriFromSkor($skorTotal);
        }

        $kelompokId = (int) ($mhs['kelompok_id'] ?? 0);
        $dplId      = null;

        if ($kelompokId > 0) {
            $kelompok = model(\App\Models\KelompokKknModel::class)->find($kelompokId);
            $dplId    = ! empty($kelompok['dpl_id']) ? (int) $kelompok['dpl_id'] : null;
        }

        $data = [
            'mahasiswa_id'      => (int) $mhs['id'],
            'tipe_evaluasi'     => 'mahasiswa',
            'kelompok_id'       => $kelompokId > 0 ? $kelompokId : null,
            'dpl_id'            => $dplId,
            'rating'            => $rating,
            'aspek_bimbingan'   => $bimbingan,
            'aspek_lokasi'      => $lokasi,
            'aspek_pelaksanaan' => $pelaksanaan,
            'skor_total'        => $skorTotal,
            'kategori'          => $kategori,
            'komentar'          => trim((string) $this->request->getPost('komentar')),
        ];

        $evaluasiModel = model(EvaluasiModel::class);
        $existing      = $evaluasiModel->findByMahasiswa((int) $mhs['id']);
        $isUpdate      = $existing !== null;

        if ($isUpdate) {
            $evaluasiModel->update((int) $existing['id'], $data);
            $evaluasiId = (int) $existing['id'];
        } else {
            $evaluasiId = (int) $evaluasiModel->insert($data);
        }

        AuditLib::log(
            $isUpdate ? 'update_evaluasi' : 'submit_evaluasi',
            'evaluasi',
            ($mhs['nama'] ?? 'Mahasiswa') . ' mengirim evaluasi kegiatan (rating ' . $rating . '/5)',
            $evaluasiId,
            $isUpdate ? ['rating' => $existing['rating'] ?? null] : null,
            ['rating' => $rating, 'skor_total' => $skorTotal, 'kategori' => $kategori]
        );

        $this->notifyDplOfMahasiswa(
            $mhs,
            $isUpdate ? 'Evaluasi kegiatan diperbarui' : 'Evaluasi kegiatan baru',
            ($mhs['nama'] ?? 'Mahasiswa') . ' menilai pelaksanaan KKN Tematik: ' . $rating . '/5 (' . $kategori . ').',
            'info'
        );
        $this->notifyAdmins(
            $isUpdate ? 'Evaluasi diperbarui' : 'Evaluasi kegiatan masuk',
            ($mhs['nama'] ?? 'Mahasiswa') . ' (' . ($mhs['npm'] ?? '-') . ') mengirim evaluasi kegiatan.',
            'info'
        );

        $this->pusher->trigger('kkn-channel', 'evaluasi.submitted', [
            'nama_mahasiswa' => $mhs['nama'] ?? '',
            'npm'            => $mhs['npm'] ?? '',
            'rating'         => $rating,
            'kategori'       => $kategori,
        ]);

        return redirect()->to('/mahasiswa/evaluasi')
            ->with('success', $isUpdate ? 'Evaluasi berhasil diperbarui.' : 'Evaluasi kegiatan berhasil dikirim. Terima kasih atas masukan Anda.');
    }
}
