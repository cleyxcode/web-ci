<?php
$m = $mahasiswa ?? [];
$alamatLokasi = format_alamat($m);
$alamatPenelitian = trim($m['alamat_penelitian'] ?? '');
?>

<div class="dashboard-page dashboard-mahasiswa">
    <!-- Hero Strip with Progress -->
    <div class="mb-5 relative overflow-hidden rounded-2xl bg-gradient-to-br from-violet-700 via-indigo-700 to-blue-800 p-6 text-white shadow-lg">
        <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
        
        <div class="relative z-10">
            <div class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-extrabold tracking-widest text-violet-100 backdrop-blur-sm">
                <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                <?= esc($m['periode'] ?? 'Periode KKN') ?>
            </div>
            
            <h2 class="mt-4 text-3xl font-extrabold tracking-tight text-white"><?= esc($m['nama'] ?? 'Mahasiswa') ?></h2>
            
            <div class="mt-2 flex flex-wrap items-center gap-y-2 gap-x-4 text-sm text-violet-100">
                <div class="flex items-center gap-1.5 font-semibold">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm14 10v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <?= esc($m['nama_kelompok'] ?? 'Belum ada kelompok') ?>
                </div>
                <?php if (! empty($m['npm'])): ?>
                <div class="flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    <span class="font-mono"><?= esc($m['npm']) ?></span>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="mt-3 inline-flex max-w-full items-start gap-2 rounded-xl bg-black/20 p-3 text-sm backdrop-blur-sm">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="mt-0.5 h-4 w-4 shrink-0 text-rose-400"><path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 00-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 002.682 2.282 16.975 16.975 0 001.145.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
                <?php if ($alamatLokasi !== '-'): ?>
                    <span class="leading-tight text-white/90">
                        <?= esc($alamatLokasi) ?>
                        <?php if ($alamatPenelitian !== ''): ?> <br><span class="opacity-75"><?= esc($alamatPenelitian) ?></span><?php endif; ?>
                    </span>
                <?php else: ?>
                    <span class="text-rose-200">Belum ditempatkan di kelompok KKN. Hubungi admin.</span>
                <?php endif; ?>
            </div>
            
            <div class="mt-6 max-w-md">
                <div class="flex items-center justify-between text-xs font-bold uppercase tracking-wider text-violet-100">
                    <span>Progress Logbook Tervalidasi</span>
                    <span class="js-count-up font-mono text-sm" data-count-up="<?= (int) $progress ?>" data-count-suffix="%"><?= (int) $progress ?>%</span>
                </div>
                <div class="progress-bar mt-2">
                    <span style="width: <?= (int) $progress ?>%"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat Grid -->
    <section class="mb-5 grid grid-cols-2 gap-3 xl:grid-cols-5" aria-label="Ringkasan KKN">
        
        <a href="<?= site_url('mahasiswa/logbook') ?>" class="admin-stat-card tone-violet group">
            <span class="admin-stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="h-6 w-6" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 4v15.5A2.5 2.5 0 0 1 6.5 17H20V4H6.5A2.5 2.5 0 0 0 4 6.5"/></svg>
            </span>
            <div class="min-w-0 flex-1">
                <span class="block text-xs font-bold text-slate-500">Logbook</span>
                <strong class="js-count-up block text-2xl font-extrabold text-slate-900 dark:text-white" data-count-up="<?= (int) ($totalLogbook ?? 0) ?>"><?= (int) ($totalLogbook ?? 0) ?></strong>
                <small class="text-xs text-slate-400"><?= (int) ($validLogbook ?? 0) ?> divalidasi</small>
            </div>
        </a>
        
        <a href="<?= site_url('mahasiswa/laporan') ?>" class="admin-stat-card tone-emerald group">
            <span class="admin-stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="h-6 w-6" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg>
            </span>
            <div class="min-w-0 flex-1">
                <span class="block text-xs font-bold text-slate-500">Laporan</span>
                <strong class="js-count-up block text-2xl font-extrabold text-slate-900 dark:text-white" data-count-up="<?= (int) ($totalLaporan ?? 0) ?>"><?= (int) ($totalLaporan ?? 0) ?></strong>
                <small class="text-xs text-slate-400">File terupload</small>
            </div>
        </a>

        <a href="<?= site_url('mahasiswa/evaluasi') ?>" class="admin-stat-card tone-amber group">
            <span class="admin-stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="h-6 w-6" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m12 2 3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z"/></svg>
            </span>
            <div class="min-w-0 flex-1">
                <span class="block text-xs font-bold text-slate-500">Evaluasi</span>
                <?php if (! empty($evaluasi)): ?>
                    <strong class="block text-xl font-extrabold text-slate-900 dark:text-white flex items-center gap-1 mt-0.5">
                        <span class="evaluasi-rating-stars">
                            <?php for ($i=1; $i<=5; $i++): ?>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="star-icon h-3 w-3 <?= $i <= (int) $evaluasi['rating'] ? 'filled' : '' ?>"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z"/></svg>
                            <?php endfor; ?>
                        </span>
                    </strong>
                    <small class="text-xs text-slate-400 mt-0.5 block">Terkirim</small>
                <?php else: ?>
                    <strong class="block text-xl font-extrabold text-slate-900 dark:text-white">Belum</strong>
                    <small class="text-xs text-rose-500 font-semibold">Menunggu evaluasi dari DPL</small>
                <?php endif; ?>
            </div>
        </a>

        <!-- Not clickable (Info cards) -->
        <div class="admin-stat-card tone-blue">
            <span class="admin-stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="h-6 w-6" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/></svg>
            </span>
            <div class="min-w-0 flex-1">
                <span class="block text-xs font-bold text-slate-500">Dosen Pembimbing</span>
                <strong class="block truncate text-sm font-extrabold text-slate-900 dark:text-white" title="<?= esc($m['nama_dpl'] ?? '-') ?>"><?= esc($m['nama_dpl'] ?? '-') ?></strong>
                <small class="text-xs text-slate-400">DPL Kelompok</small>
            </div>
        </div>

        <div class="admin-stat-card tone-cyan">
            <span class="admin-stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="h-6 w-6" stroke="currentColor" stroke-width="2"><rect x="5" y="4" width="14" height="16" rx="2"/><path stroke-linecap="round" d="M8 2v4m8-4v4M8 11h8"/></svg>
            </span>
            <div class="min-w-0 flex-1">
                <span class="block text-xs font-bold text-slate-500">Jadwal Pelaksanaan</span>
                <strong class="block truncate text-sm font-extrabold text-slate-900 dark:text-white"><?= format_tanggal($m['tanggal_mulai'] ?? null) ?></strong>
                <small class="block truncate text-[10px] text-slate-400">s/d <?= format_tanggal($m['tanggal_selesai'] ?? null) ?></small>
            </div>
        </div>
    </section>

    <?= view('partials/logbook-filter', [
        'filterRoute'  => site_url('mahasiswa/dashboard'),
        'filterPeriod' => $filterPeriod ?? 'minggu',
        'filterDate'   => $filterDate ?? date('Y-m-d'),
        'filterLabel'  => $filterLabel ?? 'Pilih rentang waktu',
    ]) ?>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <a href="<?= site_url('mahasiswa/logbook/create') ?>" class="quick-action">
            <div class="quick-action_svg">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            </div>
            <span class="mt-2 text-sm">Tambah Logbook</span>
        </a>
        
        <a href="<?= site_url('mahasiswa/laporan/create') ?>" class="quick-action">
            <div class="quick-action_svg tone-emerald">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-full w-full text-emerald-600"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            </div>
            <span class="mt-2 text-sm text-emerald-700 dark:text-emerald-400">Upload Laporan</span>
        </a>
        
        <a href="<?= site_url('mahasiswa/nilai') ?>" class="quick-action">
            <div class="quick-action_svg tone-amber">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-full w-full text-amber-600"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
            </div>
            <span class="mt-2 text-sm text-amber-700 dark:text-amber-400">Lihat Nilai</span>
        </a>
        
        <a href="<?= site_url('mahasiswa/evaluasi') ?>" class="quick-action">
            <div class="quick-action_svg tone-rose">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-full w-full text-rose-600"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            </div>
            <span class="mt-2 text-sm text-rose-700 dark:text-rose-400"><?= ! empty($evaluasi) ? 'Lihat Evaluasi' : 'Menunggu Evaluasi' ?></span>
        </a>
        
        <a href="<?= site_url('mahasiswa/tim') ?>" class="quick-action">
            <div class="quick-action_svg tone-blue">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-full w-full text-blue-600"><path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zm14 10v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
            </div>
            <span class="mt-2 text-sm text-blue-700 dark:text-blue-400">Tim & Lokasi KKN</span>
        </a>
    </div>

    <!-- Map -->
    <?php if (! empty($petaKelompok)): ?>
    <div class="card">
        <div class="card-head">
            <h2>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="inline h-5 w-5 text-emerald-500 mr-1"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5-2V4l5 2 6-2 5 2v14l-5-2-6 2zM9 6v14M15 4v14"/></svg>
                Lokasi KKN Kelompok
            </h2>
            <a href="<?= site_url('mahasiswa/tim') ?>" class="btn btn-secondary btn-sm">Set GPS Lokasi</a>
        </div>
        <?= view('partials/map', [
            'mapId'   => 'map-mhs-dash',
            'markers' => $petaKelompok,
            'zoom'    => 15,
            'class'   => 'map-box map-box-lg',
        ]) ?>
    </div>
    <?php endif; ?>

    <!-- Main Grids -->
    <div class="dashboard-grid dashboard-grid-wide">
        <!-- Logbook List -->
        <div class="card dashboard-panel dashboard-table-panel">
            <div class="card-head">
                <h2>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="inline h-5 w-5 text-violet-500 mr-1"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    Logbook · <?= esc($filterLabel ?? 'Periode terpilih') ?>
                </h2>
                <a href="<?= site_url('mahasiswa/logbook') ?>" class="btn btn-secondary btn-sm">Semua Logbook</a>
            </div>
            <div class="table-wrap responsive-table">
                <table class="data">
                    <thead>
                        <tr><th>Tanggal</th><th>Kegiatan</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                    <?php $rows = array_slice($logbookTerbaru ?? [], 0, 6); ?>
                    <?php if ($rows === []): ?>
                        <tr>
                            <td colspan="3">
                                <div class="flex flex-col items-center p-6 text-center">
                                    <div class="grid h-16 w-16 place-items-center rounded-full bg-slate-100 text-slate-400 dark:bg-slate-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" class="h-8 w-8"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </div>
                                    <strong class="mt-4 block text-sm font-extrabold text-slate-900 dark:text-white">Belum ada logbook</strong>
                                    <p class="mt-1 text-sm text-slate-500">Mulai catat kegiatan KKN harian Anda.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rows as $row): ?>
                            <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                <td data-label="Tanggal" class="font-semibold text-slate-700 dark:text-slate-300"><?= format_tanggal($row['tanggal'] ?? null) ?></td>
                                <td data-label="Kegiatan"><?= esc(mb_strimwidth($row['kegiatan'], 0, 80, '…')) ?></td>
                                <td data-label="Status"><span class="stempel <?= stempel_class($row['status']) ?>"><?= stempel_label($row['status']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Announcements -->
        <div class="card dashboard-panel">
            <div class="card-head">
                <h2>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="inline h-5 w-5 text-amber-500 mr-1"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                    Pengumuman Kampus
                </h2>
            </div>
            
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                <?php if (empty($pengumuman)): ?>
                    <div class="py-10 text-center">
                        <span class="text-3xl opacity-50">📭</span>
                        <p class="mt-3 text-sm text-slate-400">Belum ada pengumuman terbaru.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($pengumuman as $p): ?>
                        <div class="announce-item group cursor-pointer p-4 transition hover:bg-slate-50 dark:hover:bg-slate-800/50">
                            <div class="flex items-center justify-between">
                                <strong class="text-sm font-extrabold text-slate-900 transition group-hover:text-violet-600 dark:text-white dark:group-hover:text-violet-400"><?= esc($p['judul']) ?></strong>
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-500 dark:bg-slate-800 dark:text-slate-400"><?= format_tanggal($p['created_at'] ?? null) ?></span>
                            </div>
                            <p class="mt-1 text-xs leading-relaxed text-slate-500 dark:text-slate-400"><?= esc(mb_strimwidth($p['isi'], 0, 120, '…')) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
