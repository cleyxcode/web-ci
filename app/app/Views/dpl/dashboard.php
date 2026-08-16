<?php if (empty($dpl)): ?>
    <div class="card"><p class="empty text-rose-500">Profil DPL belum terhubung ke akun Anda. Hubungi admin.</p></div>
<?php else: ?>
<div class="dashboard-page dashboard-dpl">
    <!-- Hero Strip -->
    <div class="mb-5 flex flex-col justify-between gap-4 overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-700 via-teal-600 to-emerald-600 p-6 text-white shadow-lg sm:flex-row sm:items-center">
        <div>
            <div class="text-xs font-extrabold uppercase tracking-widest text-emerald-200">Antrian validasi DPL</div>
            <h2 class="mt-1 text-2xl font-extrabold">Halo, <?= esc($dpl['nama']) ?> 👋</h2>
            <p class="mt-1 text-sm text-emerald-100">Prioritaskan logbook dan laporan mahasiswa yang menunggu stempel validasi Anda.</p>
        </div>
        <div class="flex items-center gap-4 rounded-xl bg-white/10 p-3 ring-1 ring-white/20 backdrop-blur-sm">
            <div class="flex flex-col">
                <span class="text-xs font-semibold text-emerald-100">Perlu Tindakan</span>
                <strong class="js-count-up text-3xl font-extrabold text-white" data-count-up="<?= (int) ($pendingTotal ?? (count($logbookPending ?? []) + count($laporanPending ?? []))) ?>"><?= (int) ($pendingTotal ?? (count($logbookPending ?? []) + count($laporanPending ?? []))) ?></strong>
            </div>
            <div class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-white text-emerald-600 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
        </div>
    </div>

    <!-- Stat Cards (Clickable) -->
    <section class="mb-5 grid grid-cols-2 gap-3 xl:grid-cols-4" aria-label="Ringkasan bimbingan">
        <a href="<?= site_url('dpl/monitoring') ?>" class="admin-stat-card tone-emerald group">
            <span class="admin-stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="h-6 w-6" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm13 10v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </span>
            <span class="min-w-0 flex-1">
                <span class="block text-xs font-bold text-slate-500">Mahasiswa</span>
                <strong class="js-count-up block text-2xl font-extrabold text-slate-900 dark:text-white" data-count-up="<?= (int) ($jumlahMahasiswa ?? 0) ?>"><?= (int) ($jumlahMahasiswa ?? 0) ?></strong>
                <small class="text-xs text-slate-400">Aktif bimbingan</small>
            </span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-4 w-4 shrink-0 text-slate-300 opacity-0 transition group-hover:opacity-100 dark:text-slate-600"><path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/></svg>
        </a>

        <a href="<?= site_url('dpl/logbook?status=menunggu') ?>" class="admin-stat-card tone-amber group">
            <span class="admin-stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="h-6 w-6" stroke="currentColor" stroke-width="2"><rect x="5" y="4" width="14" height="16" rx="2"/><path stroke-linecap="round" d="M8 2v4m8-4v4M8 11h8m-8 4h5"/></svg>
            </span>
            <span class="min-w-0 flex-1">
                <span class="block text-xs font-bold text-slate-500">Logbook</span>
                <strong class="js-count-up block text-2xl font-extrabold text-slate-900 dark:text-white" data-count-up="<?= is_array($logbookPending ?? null) ? count($logbookPending) : (int) ($logbookPending ?? 0) ?>"><?= is_array($logbookPending ?? null) ? count($logbookPending) : (int) ($logbookPending ?? 0) ?></strong>
                <small class="text-xs text-slate-400">Menunggu validasi</small>
            </span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-4 w-4 shrink-0 text-slate-300 opacity-0 transition group-hover:opacity-100 dark:text-slate-600"><path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/></svg>
        </a>

        <a href="<?= site_url('dpl/laporan?status=menunggu') ?>" class="admin-stat-card tone-rose group">
            <span class="admin-stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="h-6 w-6" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6Zm0 0v6h6M8 13h8M8 17h5"/></svg>
            </span>
            <span class="min-w-0 flex-1">
                <span class="block text-xs font-bold text-slate-500">Laporan</span>
                <strong class="js-count-up block text-2xl font-extrabold text-slate-900 dark:text-white" data-count-up="<?= is_array($laporanPending ?? null) ? count($laporanPending) : (int) ($laporanPending ?? 0) ?>"><?= is_array($laporanPending ?? null) ? count($laporanPending) : (int) ($laporanPending ?? 0) ?></strong>
                <small class="text-xs text-slate-400">Menunggu review</small>
            </span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-4 w-4 shrink-0 text-slate-300 opacity-0 transition group-hover:opacity-100 dark:text-slate-600"><path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/></svg>
        </a>

        <a href="<?= site_url('dpl/evaluasi') ?>" class="admin-stat-card tone-violet group">
            <span class="admin-stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="h-6 w-6" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m12 2 3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z"/></svg>
            </span>
            <span class="min-w-0 flex-1">
                <span class="block text-xs font-bold text-slate-500">Evaluasi Masuk</span>
                <strong class="js-count-up block text-2xl font-extrabold text-slate-900 dark:text-white" data-count-up="<?= (int) ($totalEvaluasi ?? 0) ?>"><?= (int) ($totalEvaluasi ?? 0) ?></strong>
                <span class="mt-1 flex items-center gap-1 text-xs text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="star-icon filled h-3 w-3"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z"/></svg>
                    <?= $avgEvaluasi !== null ? esc((string) $avgEvaluasi) : '-' ?> avg
                </span>
            </span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-4 w-4 shrink-0 text-slate-300 opacity-0 transition group-hover:opacity-100 dark:text-slate-600"><path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/></svg>
        </a>
    </section>

    <?= view('partials/logbook-filter', [
        'filterRoute'  => site_url('dpl/dashboard'),
        'filterPeriod' => $filterPeriod ?? 'minggu',
        'filterDate'   => $filterDate ?? date('Y-m-d'),
        'filterLabel'  => $filterLabel ?? 'Pilih rentang waktu',
    ]) ?>

    <!-- Workspace Grid -->
    <div class="dpl-workspace-grid">
        <div class="card dpl-map-card flex flex-col">
            <div class="card-head">
                <h2>Prioritas Hari Ini</h2>
                <a href="<?= site_url('dpl/logbook?status=menunggu') ?>" class="btn btn-secondary btn-sm">Lihat Antrian Lengkap</a>
            </div>
            
            <div class="flex-1 space-y-3">
                <?php if (empty($logbookPending) && empty($laporanPending)): ?>
                    <div class="flex h-full flex-col items-center justify-center p-6 text-center">
                        <div class="mb-3 grid h-14 w-14 place-items-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-8 w-8"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Antrian Bersih!</h3>
                        <p class="mt-1 text-xs text-slate-500">Semua pengajuan telah Anda proses hari ini.</p>
                    </div>
                <?php else: ?>
                    <?php foreach (array_slice($logbookPending ?? [], 0, 3) as $row): ?>
                        <div class="dpl-queue-item flex flex-wrap items-center justify-between gap-4 rounded-xl border border-amber-100 bg-amber-50/50 p-4 transition hover:bg-amber-50 dark:border-amber-900/30 dark:bg-amber-900/10 dark:hover:bg-amber-900/20">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex rounded-full bg-amber-200/50 px-2 py-0.5 text-[10px] font-extrabold tracking-wider text-amber-800 dark:bg-amber-900/50 dark:text-amber-300">LOGBOOK</span>
                                    <strong class="truncate text-sm font-bold text-slate-900 dark:text-white"><?= esc($row['nama_mahasiswa'] ?? '-') ?></strong>
                                </div>
                                <div class="mt-1 text-xs text-slate-500">
                                    <?= esc($row['nama_kelompok'] ?? 'Tanpa kelompok') ?> · <span class="font-semibold text-slate-700 dark:text-slate-300"><?= format_tanggal($row['tanggal'] ?? null) ?></span>
                                </div>
                            </div>
                            <a href="<?= site_url('dpl/logbook?status=menunggu') ?>" class="btn btn-primary btn-sm shrink-0 shadow-sm shadow-violet-500/20">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="mr-1 h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                                Proses
                            </a>
                        </div>
                    <?php endforeach; ?>

                    <?php foreach (array_slice($laporanPending ?? [], 0, 3) as $row): ?>
                        <div class="dpl-queue-item flex flex-wrap items-center justify-between gap-4 rounded-xl border border-rose-100 bg-rose-50/50 p-4 transition hover:bg-rose-50 dark:border-rose-900/30 dark:bg-rose-900/10 dark:hover:bg-rose-900/20">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex rounded-full bg-rose-200/50 px-2 py-0.5 text-[10px] font-extrabold tracking-wider text-rose-800 dark:bg-rose-900/50 dark:text-rose-300">LAPORAN</span>
                                    <strong class="truncate text-sm font-bold text-slate-900 dark:text-white"><?= esc($row['nama_mahasiswa'] ?? '-') ?></strong>
                                </div>
                                <div class="mt-1 truncate text-xs text-slate-500">
                                    <?= esc($row['nama_kelompok'] ?? 'Tanpa kelompok') ?> · <span class="font-semibold text-slate-700 dark:text-slate-300"><?= esc($row['judul'] ?? '-') ?></span>
                                </div>
                            </div>
                            <a href="<?= site_url('dpl/laporan?status=menunggu') ?>" class="btn btn-primary btn-sm shrink-0 bg-rose-600 shadow-sm shadow-rose-500/20 hover:bg-rose-700">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="mr-1 h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Review
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="card flex flex-col">
            <div class="card-head">
                <h2>Kelompok Bimbingan</h2>
                <a href="<?= site_url('dpl/monitoring') ?>" class="btn btn-secondary btn-sm">Detail Monitoring</a>
            </div>
            
            <div class="flex-1">
                <?php if (empty($kelompok)): ?>
                    <div class="flex flex-col items-center justify-center py-12 px-6 text-center">
                        <div class="mx-auto mb-4 grid h-14 w-14 place-items-center rounded-2xl bg-slate-100 text-slate-300 dark:bg-slate-800 dark:text-slate-600">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" class="h-7 w-7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Belum ada kelompok</p>
                        <p class="mt-1 text-xs text-slate-400">Admin kampus belum menetapkan kelompok untuk Anda</p>
                    </div>
                <?php else: ?>
                    <div class="dpl-group-list space-y-3">
                        <?php foreach ($kelompok as $group): ?>
                            <div class="dpl-group-item flex items-center justify-between gap-3 rounded-xl border border-slate-100 bg-white p-4 shadow-sm transition hover:border-slate-200 dark:border-slate-800 dark:bg-slate-800/50 dark:hover:border-slate-700">
                                <div>
                                    <strong class="block text-sm text-slate-900 dark:text-white"><?= esc($group['nama_kelompok']) ?></strong>
                                    <small class="mt-1 block text-xs text-slate-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="inline h-3.5 w-3.5 mr-0.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <?= esc(format_alamat($group)) ?> · 
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="inline h-3.5 w-3.5 mr-0.5 ml-1"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                        <?= (int) ($group['jumlah_anggota'] ?? 0) ?> mahasiswa
                                    </small>
                                </div>
                                <span class="stempel <?= ! empty($group['latitude']) && ! empty($group['longitude']) ? 'stempel-divalidasi' : 'stempel-menunggu' ?>">
                                    <?= ! empty($group['latitude']) && ! empty($group['longitude']) ? '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3 w-3"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> GPS Aktif' : '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3 w-3"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg> Belum ada GPS' ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Map Section -->
    <div class="card">
        <div class="card-head">
            <h2>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="inline h-5 w-5 text-emerald-500 mr-1"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5-2V4l5 2 6-2 5 2v14l-5-2-6 2zM9 6v14M15 4v14"/></svg>
                Peta Lokasi Kelompok Bimbingan
            </h2>
        </div>
        <?= view('partials/map', [
            'mapId'   => 'map-dpl-dash',
            'markers' => $petaKelompok ?? [],
            'zoom'    => 11,
            'class'   => 'map-box map-box-lg',
            'empty'   => 'Belum ada kelompok bimbingan yang menetapkan koordinat GPS.',
        ]) ?>
    </div>

    <!-- Activities Table -->
    <div class="card">
        <div class="card-head">
            <h2>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="inline h-5 w-5 text-violet-500 mr-1"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Kegiatan Mahasiswa · <?= esc($filterLabel ?? 'Periode terpilih') ?>
            </h2>
            <div class="actions">
                <a href="<?= site_url('dpl/evaluasi') ?>" class="btn btn-secondary btn-sm">Lihat Evaluasi</a>
                <a href="<?= site_url('dpl/logbook') ?>" class="btn btn-primary btn-sm">Validasi Logbook</a>
            </div>
        </div>
        <div class="table-wrap responsive-table">
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
                    <tr>
                        <td colspan="4">
                            <div class="flex flex-col items-center justify-center py-12 px-6 text-center">
                                <div class="mx-auto mb-4 grid h-14 w-14 place-items-center rounded-2xl bg-slate-100 text-slate-300 dark:bg-slate-800 dark:text-slate-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" class="h-7 w-7">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Belum ada kegiatan</p>
                                <p class="mt-1 text-xs text-slate-400">Belum ada kegiatan yang dilaporkan pada periode ini.</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rows as $row): ?>
                        <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/50">
                            <td data-label="Tanggal" class="font-semibold text-slate-700 dark:text-slate-300"><?= format_tanggal($row['tanggal'] ?? null) ?></td>
                            <td data-label="Mahasiswa">
                                <strong class="text-slate-900 dark:text-white"><?= esc($row['nama_mahasiswa'] ?? $row['nama'] ?? '-') ?></strong>
                            </td>
                            <td data-label="Kegiatan"><?= esc(mb_strimwidth($row['kegiatan'] ?? '', 0, 80, '…')) ?></td>
                            <td data-label="Status"><span class="stempel <?= stempel_class($row['status']) ?>"><?= stempel_label($row['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>
