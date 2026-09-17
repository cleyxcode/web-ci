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
    protected LokasiKknModel $lokasiModel;

    public function __construct()
    {
        $this->kelompokModel  = model(KelompokKknModel::class);
        $this->mahasiswaModel = model(MahasiswaModel::class);
        $this->lokasiModel    = model(LokasiKknModel::class);
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
            'lokasi'    => $this->lokasiModel->orderBy('nama_desa', 'ASC')->findAll(),
            'mahasiswa' => $this->mahasiswaModel->getUnassigned(),
            'belumDitempatkan' => $this->mahasiswaModel->getUnassigned(),
        ]);
    }

    public function store()
    {
        if (! $this->validateRules()) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $coordinates = $this->normalizeCoordinates();

        if ($coordinates === false) {
            return redirect()->back()->withInput()->with('error', 'Koordinat lokasi harus diisi berpasangan dan berada dalam rentang yang valid.');
        }

        $memberIds = $this->postedMemberIds();
        $leaderId  = (int) $this->request->getPost('ketua_mahasiswa_id');
        $memberError = $this->validateMemberSelection(null, $memberIds, $leaderId);

        if ($memberError !== null) {
            return redirect()->back()->withInput()->with('error', $memberError);
        }

        $db = db_connect();
        $db->transBegin();
        $lokasiId = $this->resolveLokasiId();

        if ($lokasiId === false) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Lokasi penempatan yang dipilih tidak ditemukan.');
        }

        $inserted = $this->kelompokModel->insert($this->payload($lokasiId, $coordinates, null));
        $kelompokId = (int) $this->kelompokModel->getInsertID();

        if (! $inserted || $kelompokId < 1 || ! $this->assignSelectedMembers($kelompokId, $memberIds)) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Kelompok gagal dibuat. Perubahan anggota dibatalkan.');
        }

        if (! $this->kelompokModel->update($kelompokId, ['ketua_mahasiswa_id' => $leaderId > 0 ? $leaderId : null])) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Ketua kelompok gagal disimpan.');
        }

        $db->transCommit();

        return redirect()->to('/admin/kkn/' . $kelompokId)->with('success', 'Kelompok dibuat dengan pengaturan anggota, ketua, dan lokasi yang dipilih.');
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

        $ids = $this->postedMemberIds();

        if (! $this->assignSelectedMembers($id, $ids)) {
            return redirect()->to('/admin/kkn/' . $id)->with('error', 'Mahasiswa yang sudah berada di kelompok lain tidak dapat dipindahkan dari sini.');
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
            'lokasi'    => $this->lokasiModel->orderBy('nama_desa', 'ASC')->findAll(),
            'mahasiswa' => $this->mahasiswaModel->getByKelompokId($id),
            'belumDitempatkan' => $this->mahasiswaModel->getUnassigned(),
        ]);
    }

    public function update(int $id)
    {
        if (! $this->kelompokModel->find($id)) {
            return redirect()->to('/admin/kkn')->with('error', 'Data tidak ditemukan.');
        }

        if (! $this->validateRules()) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $coordinates = $this->normalizeCoordinates();

        if ($coordinates === false) {
            return redirect()->back()->withInput()->with('error', 'Koordinat lokasi harus diisi berpasangan dan berada dalam rentang yang valid.');
        }

        $memberIds = $this->postedMemberIds();
        $leaderId  = (int) $this->request->getPost('ketua_mahasiswa_id');
        $memberError = $this->validateMemberSelection($id, $memberIds, $leaderId);

        if ($memberError !== null) {
            return redirect()->back()->withInput()->with('error', $memberError);
        }

        $db = db_connect();
        $db->transBegin();
        $lokasiId = $this->resolveLokasiId();

        if ($lokasiId === false) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Lokasi penempatan yang dipilih tidak ditemukan.');
        }

        if (! $this->assignSelectedMembers($id, $memberIds) || ! $this->kelompokModel->update($id, $this->payload($lokasiId, $coordinates, $leaderId > 0 ? $leaderId : null))) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Data kelompok gagal disimpan. Perubahan dibatalkan.');
        }

        $db->transCommit();

        return redirect()->to('/admin/kkn/' . $id)->with('success', 'Data kelompok KKN diperbarui.');
    }

    public function delete(int $id)
    {
        $this->mahasiswaModel->where('kelompok_id', $id)->set(['kelompok_id' => null])->update();
        $this->kelompokModel->delete($id);

        return redirect()->to('/admin/kkn')->with('success', 'Kelompok KKN dihapus.');
    }

    private function payload(?int $lokasiId = null, ?array $coordinates = null, ?int $leaderId = null): array
    {
        $latitude  = $coordinates[0] ?? null;
        $longitude = $coordinates[1] ?? null;

        return [
            'nama_kelompok'          => $this->request->getPost('nama_kelompok'),
            'dpl_id'                 => $this->request->getPost('dpl_id') ?: null,
            'lokasi_id'              => $lokasiId,
            'ketua_mahasiswa_id'     => $leaderId,
            'periode'                => $this->request->getPost('periode'),
            'tanggal_mulai'          => $this->request->getPost('tanggal_mulai') ?: null,
            'tanggal_selesai'        => $this->request->getPost('tanggal_selesai') ?: null,
            'alamat_penelitian'      => $this->request->getPost('alamat_penelitian'),
            'latitude'               => $latitude,
            'longitude'              => $longitude,
            'lokasi_gps_at'          => $latitude !== null && $longitude !== null ? date('Y-m-d H:i:s') : null,
        ];
    }

    private function validateRules(): bool
    {
        return $this->validate([
            'nama_kelompok'      => 'required|min_length[3]|max_length[100]',
            'dpl_id'             => 'permit_empty|is_natural_no_zero',
            'lokasi_id'          => 'permit_empty|is_natural_no_zero',
            'ketua_mahasiswa_id' => 'permit_empty|is_natural_no_zero',
            'periode'            => 'permit_empty|max_length[50]',
            'tanggal_mulai'      => 'permit_empty|valid_date[Y-m-d]',
            'tanggal_selesai'    => 'permit_empty|valid_date[Y-m-d]',
            'alamat_penelitian'  => 'permit_empty|max_length[255]',
            'new_lokasi_nama_desa' => 'permit_empty|min_length[3]|max_length[100]',
            'new_lokasi_kecamatan' => 'permit_empty|max_length[100]',
            'new_lokasi_kabupaten' => 'permit_empty|max_length[100]',
            'latitude'             => 'permit_empty|max_length[30]',
            'longitude'            => 'permit_empty|max_length[30]',
        ]);
    }

    private function normalizeCoordinates(): array|false
    {
        $latitude  = trim((string) $this->request->getPost('latitude'));
        $longitude = trim((string) $this->request->getPost('longitude'));

        if ($latitude === '' && $longitude === '') {
            return [null, null];
        }

        if ($latitude === '' || $longitude === '' || ! is_numeric($latitude) || ! is_numeric($longitude)) {
            return false;
        }

        $latitude  = (float) $latitude;
        $longitude = (float) $longitude;

        if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
            return false;
        }

        return [$latitude, $longitude];
    }

    private function postedMemberIds(): array
    {
        $ids = $this->request->getPost('mahasiswa_ids');

        if (! is_array($ids)) {
            return [];
        }

        $ids = array_map(static fn ($id): int => (int) $id, $ids);
        $ids = array_filter($ids, static fn (int $id): bool => $id > 0);

        return array_values(array_unique($ids));
    }

    private function validateMemberSelection(?int $kelompokId, array $memberIds, int $leaderId): ?string
    {
        foreach ($memberIds as $memberId) {
            $mahasiswa = $this->mahasiswaModel->find($memberId);

            if (! $mahasiswa) {
                return 'Ada mahasiswa yang tidak ditemukan.';
            }

            $currentGroupId = (int) ($mahasiswa['kelompok_id'] ?? 0);

            if ($currentGroupId > 0 && $currentGroupId !== $kelompokId) {
                return esc($mahasiswa['nama']) . ' sudah berada di kelompok lain.';
            }
        }

        if ($leaderId < 1) {
            return null;
        }

        $leader = $this->mahasiswaModel->find($leaderId);

        if (! $leader) {
            return 'Mahasiswa yang dipilih sebagai ketua tidak ditemukan.';
        }

        $leaderIsMember = in_array($leaderId, $memberIds, true)
            || ($kelompokId !== null && (int) ($leader['kelompok_id'] ?? 0) === $kelompokId);

        if (! $leaderIsMember) {
            return 'Ketua harus dipilih sebagai anggota kelompok.';
        }

        return null;
    }

    private function assignSelectedMembers(int $kelompokId, array $memberIds): bool
    {
        foreach ($memberIds as $memberId) {
            $mahasiswa = $this->mahasiswaModel->find($memberId);

            if (! $mahasiswa || ((int) ($mahasiswa['kelompok_id'] ?? 0) > 0 && (int) $mahasiswa['kelompok_id'] !== $kelompokId)) {
                return false;
            }

            if ((int) ($mahasiswa['kelompok_id'] ?? 0) !== $kelompokId
                && ! $this->mahasiswaModel->update($memberId, ['kelompok_id' => $kelompokId])) {
                return false;
            }
        }

        return true;
    }

    private function resolveLokasiId(): int|false|null
    {
        $selectedId = (int) $this->request->getPost('lokasi_id');

        if ($selectedId > 0) {
            return $this->lokasiModel->find($selectedId) ? $selectedId : false;
        }

        $namaDesa = trim((string) $this->request->getPost('new_lokasi_nama_desa'));

        if ($namaDesa === '') {
            return null;
        }

        $data = [
            'nama_desa' => $namaDesa,
            'kecamatan' => trim((string) $this->request->getPost('new_lokasi_kecamatan')) ?: null,
            'kabupaten' => trim((string) $this->request->getPost('new_lokasi_kabupaten')) ?: null,
        ];

        $existing = $this->lokasiModel
            ->where('nama_desa', $data['nama_desa'])
            ->where('kecamatan', $data['kecamatan'])
            ->where('kabupaten', $data['kabupaten'])
            ->first();

        if ($existing) {
            return (int) $existing['id'];
        }

        if (! $this->lokasiModel->insert($data)) {
            return false;
        }

        $locationId = (int) $this->lokasiModel->getInsertID();

        return $locationId > 0 ? $locationId : false;
    }
}
