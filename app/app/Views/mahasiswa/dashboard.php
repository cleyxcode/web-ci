<?php
$m = $mahasiswa ?? [];
$alamatLokasi = format_alamat($m);
$alamatPenelitian = trim($m['alamat_penelitian'] ?? '');
?>

<div class="dashboard-page dashboard-mahasiswa">
<div class="hero-strip">
    <div class="periode"><?= esc($m['periode'] ?? 'Periode KKN') ?></div>
    <h2 style="margin:0 0 4px;font-size:1.25rem"><?= esc($m['nama'] ?? 'Mahasiswa') ?></h2>
    <p style="margin:0 0 4px;color:var(--abu-karang);font-size:0.9rem">
        <?= esc($m['nama_kelompok'] ?? 'Belum ada kelompok') ?>
        <?php if (! empty($m['npm'])): ?> · <span class="font-mono"><?= esc($m['npm']) ?></span><?php endif; ?>
    </p>
    <?php if ($alamatLokasi !== '-'): ?>
        <p style="margin:0 0 16px;font-size:0.85rem;color:var(--tinta-redup)">
            📍 <?= esc($alamatLokasi) ?>
            <?php if ($alamatPenelitian !== ''): ?> — <?= esc($alamatPenelitian) ?><?php endif; ?>
        </p>
    <?php else: ?>
        <p style="margin:0 0 16px;font-size:0.85rem;color:var(--kuning-senja)">Belum ditempatkan di kelompok KKN. Hubungi admin.</p>
    <?php endif; ?>
    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:8px">
        <span style="font-size:0.8rem;color:var(--abu-karang)">Progress logbook tervalidasi</span>
        <span class="font-mono" style="font-weight:700"><?= (int) $progress ?>%</span>
    </div>
    <div class="progress-bar"><span style="width:<?= (int) $progress ?>%"></span></div>
</div>

<div class="stat-row dashboard-stat-row">
    <div class="stat">
        <div class="label">Logbook</div>
        <div class="value"><?= (int) ($totalLogbook ?? 0) ?></div>
        <small><?= (int) ($validLogbook ?? 0) ?> divalidasi</small>
    </div>
    <div class="stat">
        <div class="label">Laporan</div>
        <div class="value"><?= (int) ($totalLaporan ?? 0) ?></div>
        <small>file terupload</small>
    </div>
    <div class="stat">
        <div class="label">Evaluasi</div>
        <div class="value" style="font-size:1rem">
            <?php if (! empty($evaluasi)): ?>
                <?= (int) $evaluasi['rating'] ?>/5
            <?php else: ?>
                Belum
            <?php endif; ?>
        </div>
        <small><?= ! empty($evaluasi) ? 'sudah dikirim' : 'isi evaluasi kegiatan' ?></small>
    </div>
    <div class="stat">
        <div class="label">DPL</div>
        <div class="value" style="font-size:1rem"><?= esc($m['nama_dpl'] ?? '-') ?></div>
    </div>
    <div class="stat">
        <div class="label">Periode</div>
        <div class="value" style="font-size:0.95rem"><?= format_tanggal($m['tanggal_mulai'] ?? null) ?></div>
        <small>s/d <?= format_tanggal($m['tanggal_selesai'] ?? null) ?></small>
    </div>
</div>

<?php if (! empty($petaKelompok)): ?>
<div class="card" style="margin-bottom:16px">
    <div class="card-head">
        <h2>Lokasi KKN kelompok</h2>
        <a href="<?= site_url('mahasiswa/tim') ?>" class="btn btn-secondary btn-sm">Tim & GPS</a>
    </div>
    <?= view('partials/map', [
        'mapId'   => 'map-mhs-dash',
        'markers' => $petaKelompok,
        'zoom'    => 15,
        'class'   => 'map-box',
    ]) ?>
</div>
<?php endif; ?>

<div class="quick-actions dashboard-quick-actions">
    <a href="<?= site_url('mahasiswa/logbook/create') ?>" class="quick-action">
        <?= view('partials/icon', ['name' => 'book']) ?>
        <span>Tambah logbook</span>
    </a>
    <a href="<?= site_url('mahasiswa/laporan/create') ?>" class="quick-action">
        <?= view('partials/icon', ['name' => 'upload']) ?>
        <span>Upload laporan</span>
    </a>
    <a href="<?= site_url('mahasiswa/nilai') ?>" class="quick-action">
        <?= view('partials/icon', ['name' => 'star']) ?>
        <span>Lihat nilai</span>
    </a>
    <a href="<?= site_url('mahasiswa/evaluasi') ?>" class="quick-action">
        <?= view('partials/icon', ['name' => 'chat']) ?>
        <span><?= ! empty($evaluasi) ? 'Lihat evaluasi' : 'Isi evaluasi' ?></span>
    </a>
    <a href="<?= site_url('mahasiswa/tim') ?>" class="quick-action">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm14 10v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        <span>Tim KKN</span>
    </a>
</div>

<div class="dashboard-grid dashboard-grid-wide">
    <div class="card dashboard-panel dashboard-table-panel">
        <div class="card-head">
            <h2>Logbook terbaru</h2>
            <a href="<?= site_url('mahasiswa/logbook') ?>" class="btn btn-secondary btn-sm">Semua</a>
        </div>
        <div class="table-wrap">
            <table class="data">
                <thead>
                    <tr><th>Tanggal</th><th>Kegiatan</th><th>Status</th></tr>
                </thead>
                <tbody>
                <?php $rows = array_slice($logbookTerbaru ?? [], 0, 6); ?>
                <?php if ($rows === []): ?>
                    <tr><td colspan="3" class="empty">Belum ada logbook. Mulai catat kegiatan harian Anda.</td></tr>
                <?php else: ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?= format_tanggal($row['tanggal'] ?? null) ?></td>
                            <td><?= esc(mb_strimwidth($row['kegiatan'], 0, 50, '…')) ?></td>
                            <td><span class="<?= stempel_class($row['status']) ?>"><?= stempel_label($row['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card dashboard-panel">
        <div class="card-head"><h2>Pengumuman</h2></div>
        <?php if (empty($pengumuman)): ?>
            <p class="empty" style="padding:16px 0">Belum ada pengumuman.</p>
        <?php else: ?>
            <?php foreach ($pengumuman as $p): ?>
                <div class="announce-item">
                    <strong><?= esc($p['judul']) ?></strong>
                    <span class="field-hint"><?= format_tanggal($p['created_at'] ?? null) ?></span>
                    <p><?= esc(mb_strimwidth($p['isi'], 0, 120, '…')) ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</div>
