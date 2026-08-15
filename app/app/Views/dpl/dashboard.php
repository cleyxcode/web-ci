<?php if (empty($dpl)): ?>
    <div class="card"><p class="empty">Profil DPL belum terhubung ke akun Anda. Hubungi admin.</p></div>
<?php else: ?>
<div class="dashboard-page dashboard-dpl">
    <div class="hero-strip">
        <div class="periode">Antrian validasi</div>
        <h2 style="margin:0 0 8px;font-size:1.25rem">Halo, <?= esc($dpl['nama']) ?></h2>
        <p style="margin:0;color:var(--abu-karang);font-size:0.9rem">Prioritaskan logbook dan laporan yang menunggu stempel validasi.</p>
    </div>

    <div class="stat-row dashboard-stat-row">
        <div class="stat stat-tone-blue">
            <div class="label">Mahasiswa bimbingan</div>
            <div class="value"><?= (int) ($jumlahMahasiswa ?? 0) ?></div>
        </div>
        <div class="stat stat-tone-coral">
            <div class="label">Logbook menunggu</div>
            <div class="value"><?= is_array($logbookPending ?? null) ? count($logbookPending) : (int) ($logbookPending ?? 0) ?></div>
        </div>
        <div class="stat stat-tone-amber">
            <div class="label">Laporan menunggu</div>
            <div class="value"><?= is_array($laporanPending ?? null) ? count($laporanPending) : (int) ($laporanPending ?? 0) ?></div>
        </div>
        <div class="stat stat-tone-violet">
            <div class="label">Evaluasi masuk</div>
            <div class="value"><?= (int) ($totalEvaluasi ?? 0) ?></div>
            <small>avg <?= $avgEvaluasi !== null ? esc((string) $avgEvaluasi) : '-' ?>/5</small>
    </div>
    </div>

    <?= view('partials/logbook-filter', [
        'filterRoute'  => site_url('dpl/dashboard'),
        'filterPeriod' => $filterPeriod ?? 'minggu',
        'filterDate'   => $filterDate ?? date('Y-m-d'),
        'filterLabel'  => $filterLabel ?? 'Pilih rentang waktu',
    ]) ?>

    <div class="dpl-workspace-grid" style="margin-bottom:16px">
        <div class="card">
            <div class="card-head">
                <h2>Prioritas hari ini</h2>
                <a href="<?= site_url('dpl/logbook?status=menunggu') ?>" class="btn btn-secondary btn-sm">Buka antrian</a>
            </div>
            <?php if (empty($logbookPending) && empty($laporanPending)): ?>
                <p class="empty">Semua pengajuan sudah diproses. Antrian Anda bersih.</p>
            <?php else: ?>
                <?php foreach (array_slice($logbookPending ?? [], 0, 3) as $row): ?>
                    <div class="dpl-queue-item">
                        <div>
                            <strong>Logbook · <?= esc($row['nama_mahasiswa'] ?? '-') ?></strong>
                            <small><?= esc($row['nama_kelompok'] ?? 'Tanpa kelompok') ?> · <?= format_tanggal($row['tanggal'] ?? null) ?></small>
                        </div>
                        <a href="<?= site_url('dpl/logbook?status=menunggu') ?>" class="btn btn-primary btn-sm">Proses</a>
                    </div>
                <?php endforeach; ?>
                <?php foreach (array_slice($laporanPending ?? [], 0, 3) as $row): ?>
                    <div class="dpl-queue-item">
                        <div>
                            <strong>Laporan · <?= esc($row['nama_mahasiswa'] ?? '-') ?></strong>
                            <small><?= esc($row['nama_kelompok'] ?? 'Tanpa kelompok') ?> · <?= esc($row['judul'] ?? '-') ?></small>
                        </div>
                        <a href="<?= site_url('dpl/laporan?status=menunggu') ?>" class="btn btn-primary btn-sm">Review</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="card">
            <div class="card-head">
                <h2>Kelompok bimbingan</h2>
                <a href="<?= site_url('dpl/monitoring') ?>" class="btn btn-secondary btn-sm">Monitoring</a>
            </div>
            <?php if (empty($kelompok)): ?>
                <p class="empty">Belum ada kelompok yang ditetapkan admin untuk Anda.</p>
            <?php else: ?>
                <div class="dpl-group-list">
                    <?php foreach ($kelompok as $group): ?>
                        <div class="dpl-group-item">
                            <div>
                                <strong><?= esc($group['nama_kelompok']) ?></strong>
                                <small><?= esc(format_alamat($group)) ?> · <?= (int) ($group['jumlah_anggota'] ?? 0) ?> mahasiswa</small>
                            </div>
                            <span class="stempel <?= ! empty($group['latitude']) && ! empty($group['longitude']) ? 'stempel-divalidasi' : 'stempel-menunggu' ?>">
                                <?= ! empty($group['latitude']) && ! empty($group['longitude']) ? 'Ada titik' : 'Belum ada titik' ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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
            <h2>Kegiatan · <?= esc($filterLabel ?? 'Periode terpilih') ?></h2>
            <div style="display:flex;gap:8px">
                <a href="<?= site_url('dpl/evaluasi') ?>" class="btn btn-secondary btn-sm">Evaluasi</a>
                <a href="<?= site_url('dpl/logbook') ?>" class="btn btn-secondary btn-sm">Validasi</a>
            </div>
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
    </div>
<?php endif; ?>
