<?php
$lokasiLabel = format_alamat($kelompok);
$alamatPenelitian = trim($kelompok['alamat_penelitian'] ?? '');
$hasGps = ! empty($kelompok['latitude']) && ! empty($kelompok['longitude']);
$ketuaId = (int) ($kelompok['ketua_mahasiswa_id'] ?? 0);
?>

<div class="info-grid">
    <div class="info-card">
        <span class="info-label">Periode</span>
        <strong><?= esc($kelompok['periode'] ?? '-') ?></strong>
        <small><?= format_tanggal($kelompok['tanggal_mulai'] ?? null) ?> – <?= format_tanggal($kelompok['tanggal_selesai'] ?? null) ?></small>
    </div>
    <div class="info-card">
        <span class="info-label">DPL (Dosen Pembimbing Lapangan)</span>
        <strong><?= esc($kelompok['nama_dpl'] ?? 'Belum ditentukan') ?></strong>
        <?php if (! empty($kelompok['no_hp_dpl'])): ?>
            <small><?= esc($kelompok['no_hp_dpl']) ?></small>
        <?php endif; ?>
    </div>
    <div class="info-card">
        <span class="info-label">Ketua kelompok</span>
        <strong><?= esc($kelompok['nama_ketua'] ?? 'Belum ditunjuk') ?></strong>
        <?php if (! empty($kelompok['npm_ketua'])): ?>
            <small class="font-mono"><?= esc($kelompok['npm_ketua']) ?></small>
        <?php endif; ?>
    </div>
    <div class="info-card">
        <span class="info-label">Lokasi penempatan</span>
        <strong><?= esc($lokasiLabel) ?></strong>
    </div>
    <div class="info-card">
        <span class="info-label">Alamat penelitian</span>
        <strong><?= $alamatPenelitian !== '' ? esc($alamatPenelitian) : 'Belum diisi' ?></strong>
    </div>
</div>

<?php if ($hasGps): ?>
<div class="card">
    <div class="card-head">
        <h2>Titik lokasi penelitian</h2>
        <small class="field-hint">Ditetapkan <?= format_tanggal($kelompok['lokasi_gps_at'] ?? null) ?></small>
    </div>
    <?= view('partials/map', [
        'mapId'   => 'map-kelompok',
        'markers' => [$kelompok],
        'zoom'    => 15,
        'class'   => 'map-box',
    ]) ?>
</div>
<?php else: ?>
<div class="alert alert-info">Titik lokasi penelitian belum ditetapkan. Admin dapat memilihnya melalui tombol <strong>Edit info</strong>, atau ketua kelompok dapat mengirim koordinat dari menu Tim KKN.</div>
<?php endif; ?>

<div class="dash-grid">
    <div class="card">
        <div class="card-head">
            <h2>Anggota kelompok (<?= count($anggota ?? []) ?>)</h2>
            <a href="<?= site_url('admin/kkn/' . $kelompok['id'] . '/edit') ?>" class="btn btn-secondary btn-sm">Edit info</a>
        </div>
        <?php if (! empty($anggota)): ?>
        <form method="post" action="<?= site_url('admin/kkn/' . $kelompok['id'] . '/ketua') ?>">
            <?= csrf_field() ?>
            <div class="field">
                <label>Tetapkan ketua</label>
                <select name="ketua_mahasiswa_id" required>
                    <option value="">— Pilih anggota —</option>
                    <?php foreach ($anggota as $mhs): ?>
                        <option value="<?= (int) $mhs['id'] ?>" <?= $ketuaId === (int) $mhs['id'] ? 'selected' : '' ?>><?= esc($mhs['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Simpan ketua</button>
        </form>
        <?php endif; ?>
        <div class="table-wrap">
            <table class="data">
                <thead>
                    <tr><th>NPM</th><th>Nama</th><th>Prodi</th><th></th></tr>
                </thead>
                <tbody>
                <?php if (empty($anggota)): ?>
                    <tr>
                        <td colspan="4">
                            <div class="flex flex-col items-center justify-center py-14 px-6 text-center">
                                <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-2xl bg-slate-100 text-slate-300 dark:bg-slate-800 dark:text-slate-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" class="h-8 w-8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Belum ada mahasiswa</p>
                                <p class="mt-1 text-xs text-slate-400">Belum ada mahasiswa di kelompok ini.</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($anggota as $mhs): ?>
                        <tr class="<?= $ketuaId === (int) $mhs['id'] ? 'row-highlight' : '' ?>">
                            <td class="font-mono"><?= esc($mhs['npm']) ?></td>
                            <td>
                                <?= esc($mhs['nama']) ?>
                                <?php if ($ketuaId === (int) $mhs['id']): ?><span class="badge-count">Ketua</span><?php endif; ?>
                            </td>
                            <td><?= esc($mhs['prodi'] ?? '-') ?></td>
                            <td>
                                <form method="post" action="<?= site_url('admin/kkn/' . $kelompok['id'] . '/anggota/' . $mhs['id'] . '/remove') ?>" data-confirm="Keluarkan mahasiswa ini dari kelompok?">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-danger btn-sm">Keluarkan</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-head"><h2>Tambah mahasiswa</h2></div>
        <?php if (empty($belumDitempatkan)): ?>
            <div class="flex flex-col items-center justify-center py-10 px-6 text-center border-t border-slate-100 dark:border-slate-800">
                <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-2xl bg-emerald-50 text-emerald-400 shadow-sm ring-1 ring-emerald-100 dark:bg-emerald-900/20 dark:ring-emerald-900/50">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" class="h-8 w-8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                </div>
                <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Semua Mahasiswa Ditempatkan</p>
                <p class="mt-1 text-xs text-slate-400">Semua mahasiswa sudah ditempatkan di kelompok.</p>
            </div>
        <?php else: ?>
            <form method="post" action="<?= site_url('admin/kkn/' . $kelompok['id'] . '/anggota') ?>">
                <?= csrf_field() ?>
                <div class="member-pick-list">
                    <?php foreach ($belumDitempatkan as $mhs): ?>
                        <label class="member-pick">
                            <input type="checkbox" name="mahasiswa_ids[]" value="<?= (int) $mhs['id'] ?>">
                            <span>
                                <strong><?= esc($mhs['nama']) ?></strong>
                                <small class="font-mono"><?= esc($mhs['npm']) ?> · <?= esc($mhs['prodi'] ?? '-') ?></small>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Tambahkan ke kelompok</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<p>
    <a href="<?= site_url('admin/kkn') ?>" class="btn btn-secondary btn-sm">← Kembali ke daftar kelompok</a>
</p>
