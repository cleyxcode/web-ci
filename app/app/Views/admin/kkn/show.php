<?php
$lokasiLabel = format_alamat($kelompok);
$alamatPenelitian = trim($kelompok['alamat_penelitian'] ?? '');
$hasGps = ! empty($kelompok['latitude']) && ! empty($kelompok['longitude']);
$ketuaId = (int) ($kelompok['ketua_mahasiswa_id'] ?? 0);
?>

<div class="info-grid" style="margin-bottom:16px">
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
<div class="card" style="margin-bottom:16px">
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
<div class="alert alert-info" style="margin-bottom:16px">Titik lokasi penelitian belum ditetapkan. Admin dapat memilihnya melalui tombol <strong>Edit info</strong>, atau ketua kelompok dapat mengirim koordinat dari menu Tim KKN.</div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px" class="dash-grid">
    <div class="card">
        <div class="card-head">
            <h2>Anggota kelompok (<?= count($anggota ?? []) ?>)</h2>
            <a href="<?= site_url('admin/kkn/' . $kelompok['id'] . '/edit') ?>" class="btn btn-secondary btn-sm">Edit info</a>
        </div>
        <?php if (! empty($anggota)): ?>
        <form method="post" action="<?= site_url('admin/kkn/' . $kelompok['id'] . '/ketua') ?>" style="margin-bottom:12px;display:flex;gap:8px;align-items:end;flex-wrap:wrap">
            <?= csrf_field() ?>
            <div class="field" style="margin:0;flex:1;min-width:180px">
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
                    <tr><td colspan="4" class="empty">Belum ada mahasiswa di kelompok ini.</td></tr>
                <?php else: ?>
                    <?php foreach ($anggota as $mhs): ?>
                        <tr class="<?= $ketuaId === (int) $mhs['id'] ? 'row-highlight' : '' ?>">
                            <td class="font-mono"><?= esc($mhs['npm']) ?></td>
                            <td>
                                <?= esc($mhs['nama']) ?>
                                <?php if ($ketuaId === (int) $mhs['id']): ?><span class="badge-count" style="margin-left:6px">Ketua</span><?php endif; ?>
                            </td>
                            <td><?= esc($mhs['prodi'] ?? '-') ?></td>
                            <td>
                                <form method="post" action="<?= site_url('admin/kkn/' . $kelompok['id'] . '/anggota/' . $mhs['id'] . '/remove') ?>" onsubmit="return confirm('Keluarkan mahasiswa dari kelompok?')">
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
            <p class="empty">Semua mahasiswa sudah ditempatkan di kelompok.</p>
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

<p style="margin-top:12px">
    <a href="<?= site_url('admin/kkn') ?>" class="btn btn-secondary btn-sm">← Kembali ke daftar kelompok</a>
</p>
<style>@media (max-width:900px){.dash-grid{grid-template-columns:1fr!important}}</style>
