<?php

namespace App\Controllers\Admin;

use App\Controllers\PanelController;
use App\Libraries\AuditLib;
use App\Models\DplModel;
use App\Models\KelompokKknModel;
use App\Models\LokasiKknModel;
use App\Models\MahasiswaModel;

class KknController extends PanelController
{
    protected KelompokKknModel $kelompokModel;
    protected MahasiswaModel $mahasiswaModel;

    public function __construct()
    {
        $this->kelompokModel  = model(KelompokKknModel::class);
        $this->mahasiswaModel = model(MahasiswaModel::class);
    }

    public function index()
    {
        return $this->render('admin/kkn/index', [
            'title'    => 'Kelompok KKN',
            'kelompok' => $this->kelompokModel->getAllWithRelations(),
        ]);
    }

    public function create()
    {
        return $this->render('admin/kkn/form', [
            'title'     => 'Tambah Kelompok KKN',
            'dpl'       => model(DplModel::class)->findAll(),
            'lokasi'    => model(LokasiKknModel::class)->findAll(),
            'mahasiswa' => [],
        ]);
    }

    public function store()
    {
        $this->kelompokModel->insert($this->payload());

        return redirect()->to('/admin/kkn')->with('success', 'Kelompok KKN ditambahkan.');
    }

    public function show(int $id)
    {
        $kelompok = $this->kelompokModel->getDetail($id);

        if (! $kelompok) {
            return redirect()->to('/admin/kkn')->with('error', 'Kelompok tidak ditemukan.');
        }

        return $this->render('admin/kkn/show', [
            'title'            => 'Kelola ' . $kelompok['nama_kelompok'],
            'kelompok'         => $kelompok,
            'anggota'          => $this->mahasiswaModel->getByKelompokId($id),
            'belumDitempatkan' => $this->mahasiswaModel->getUnassigned(),
        ]);
    }

    public function assignAnggota(int $id)
    {
        $kelompok = $this->kelompokModel->find($id);

        if (! $kelompok) {
            return redirect()->to('/admin/kkn')->with('error', 'Kelompok tidak ditemukan.');
        }

        $ids = $this->request->getPost('mahasiswa_ids') ?? [];

        if (! is_array($ids)) {
            $ids = [];
        }

        foreach ($ids as $mhsId) {
            $mhs = $this->mahasiswaModel->find((int) $mhsId);

            if ($mhs) {
                $this->mahasiswaModel->update($mhs['id'], ['kelompok_id' => $id]);
            }
        }

        return redirect()->to('/admin/kkn/' . $id)->with('success', count($ids) . ' mahasiswa ditambahkan ke kelompok.');
    }

    public function removeAnggota(int $id, int $mhsId)
    {
        $kelompok = $this->kelompokModel->find($id);

        if (! $kelompok) {
            return redirect()->to('/admin/kkn')->with('error', 'Kelompok tidak ditemukan.');
        }

        $mhs = $this->mahasiswaModel->find($mhsId);

        if ($mhs && (int) $mhs['kelompok_id'] === $id) {
            $this->mahasiswaModel->update($mhsId, ['kelompok_id' => null]);

            if ((int) ($kelompok['ketua_mahasiswa_id'] ?? 0) === $mhsId) {
                $this->kelompokModel->update($id, ['ketua_mahasiswa_id' => null]);
            }
        }

        return redirect()->to('/admin/kkn/' . $id)->with('success', 'Mahasiswa dikeluarkan dari kelompok.');
    }

    public function setKetua(int $id)
    {
        $kelompok = $this->kelompokModel->find($id);
        $mhsId    = (int) $this->request->getPost('ketua_mahasiswa_id');

        if (! $kelompok) {
            return redirect()->to('/admin/kkn')->with('error', 'Kelompok tidak ditemukan.');
        }

        $mhs = $this->mahasiswaModel->find($mhsId);

        if (! $mhs || (int) $mhs['kelompok_id'] !== $id) {
            return redirect()->back()->with('error', 'Ketua harus anggota kelompok ini.');
        }

        $this->kelompokModel->update($id, ['ketua_mahasiswa_id' => $mhsId]);

        AuditLib::log('set_ketua', 'kelompok_kkn', 'Menetapkan ' . $mhs['nama'] . ' sebagai ketua kelompok', $id);

        $this->notify(
            (int) $mhs['user_id'],
            'Anda ditunjuk sebagai ketua',
            'Admin menunjuk Anda sebagai ketua ' . $kelompok['nama_kelompok'] . '. Hanya Anda yang dapat menetapkan lokasi GPS lapangan.',
            'success'
        );

        return redirect()->to('/admin/kkn/' . $id)->with('success', $mhs['nama'] . ' ditetapkan sebagai ketua kelompok.');
    }

    public function edit(int $id)
    {
        $kelompok = $this->kelompokModel->find($id);

        if (! $kelompok) {
            return redirect()->to('/admin/kkn')->with('error', 'Data tidak ditemukan.');
        }

        return $this->render('admin/kkn/form', [
            'title'     => 'Edit Kelompok KKN',
            'kelompok'  => $kelompok,
            'dpl'       => model(DplModel::class)->findAll(),
            'lokasi'    => model(LokasiKknModel::class)->findAll(),
            'mahasiswa' => $this->mahasiswaModel->getByKelompokId($id),
        ]);
    }

    public function update(int $id)
    {
        $this->kelompokModel->update($id, $this->payload());

        return redirect()->to('/admin/kkn')->with('success', 'Kelompok KKN diperbarui.');
    }

    public function delete(int $id)
    {
        $this->mahasiswaModel->where('kelompok_id', $id)->set(['kelompok_id' => null])->update();
        $this->kelompokModel->delete($id);

        return redirect()->to('/admin/kkn')->with('success', 'Kelompok KKN dihapus.');
    }

    private function payload(): array
    {
        return [
            'nama_kelompok'          => $this->request->getPost('nama_kelompok'),
            'dpl_id'                 => $this->request->getPost('dpl_id') ?: null,
            'lokasi_id'              => $this->request->getPost('lokasi_id') ?: null,
            'ketua_mahasiswa_id'     => $this->request->getPost('ketua_mahasiswa_id') ?: null,
            'periode'                => $this->request->getPost('periode'),
            'tanggal_mulai'          => $this->request->getPost('tanggal_mulai') ?: null,
            'tanggal_selesai'        => $this->request->getPost('tanggal_selesai') ?: null,
            'alamat_penelitian'      => $this->request->getPost('alamat_penelitian'),
            'dosen_pendamping'       => $this->request->getPost('dosen_pendamping'),
            'no_hp_dosen_pendamping' => $this->request->getPost('no_hp_dosen_pendamping'),
        ];
    }
}
