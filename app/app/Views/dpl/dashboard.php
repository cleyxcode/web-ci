<?php if (empty($dpl)): ?>
    <div class="card"><p class="empty">Profil DPL belum terhubung ke akun Anda. Hubungi admin.</p></div>
<?php else: ?>
    <div class="hero-strip">
        <div class="periode">Antrian validasi</div>
        <h2 style="margin:0 0 8px;font-size:1.25rem">Halo, <?= esc($dpl['nama']) ?></h2>
        <p style="margin:0;color:var(--abu-karang);font-size:0.9rem">Prioritaskan logbook dan laporan yang menunggu stempel validasi.</p>
    </div>

    <div class="stat-row">
        <div class="stat">
            <div class="label">Mahasiswa bimbingan</div>
            <div class="value"><?= (int) ($jumlahMahasiswa ?? 0) ?></div>
        </div>
        <div class="stat">
            <div class="label">Logbook menunggu</div>
            <div class="value"><?= is_array($logbookPending ?? null) ? count($logbookPending) : (int) ($logbookPending ?? 0) ?></div>
        </div>
        <div class="stat">
            <div class="label">Laporan menunggu</div>
            <div class="value"><?= is_array($laporanPending ?? null) ? count($laporanPending) : (int) ($laporanPending ?? 0) ?></div>
        </div>
    </div>

    <div class="card" style="margin-bottom:16px">
        <div class="card-head"><h2>Peta lokasi kelompok bimbingan</h2></div>
        <?= view('partials/map', [
            'mapId'   => 'map-dpl-dash',
            'markers' => $petaKelompok ?? [],
            'zoom'    => 11,
            'class'   => 'map-box',
            'empty'   => 'Belum ada kelompok bimbingan yang menetapkan GPS. Ketua kelompok mengirim lokasi dari menu Tim KKN.',
        ]) ?>
    </div>

    <div class="card">
        <div class="card-head">
            <h2>Kegiatan terbaru</h2>
            <a href="<?= site_url('dpl/logbook') ?>" class="btn btn-secondary btn-sm">Validasi</a>
        </div>
        <div class="table-wrap">
            <table class="data">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Mahasiswa</th>
                        <th>Kegiatan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php $rows = array_slice($kegiatanTerbaru ?? [], 0, 10); ?>
                <?php if ($rows === []): ?>
                    <tr><td colspan="4" class="empty">Belum ada kegiatan.</td></tr>
                <?php else: ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?= format_tanggal($row['tanggal'] ?? null) ?></td>
                            <td><?= esc($row['nama_mahasiswa'] ?? $row['nama'] ?? '-') ?></td>
                            <td><?= esc(mb_strimwidth($row['kegiatan'] ?? '', 0, 60, '…')) ?></td>
                            <td><span class="<?= stempel_class($row['status']) ?>"><?= stempel_label($row['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
