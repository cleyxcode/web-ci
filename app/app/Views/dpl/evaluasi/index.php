<?php
$stars = static function (?int $value): string {
    if ($value === null || $value < 1) {
        return '<span class="text-slate-300 dark:text-slate-600">-</span>';
    }
    $html = '<span class="evaluasi-rating-stars">';
    for ($i = 1; $i <= 5; $i++) {
        $html .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="star-icon h-4 w-4 shrink-0 ' . ($i <= $value ? 'filled' : '') . '"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z"/></svg>';
    }
    $html .= '</span>';
    return $html;
};

// Calculate percentages
$pctEvaluasi = $totalMahasiswa > 0 ? round(($totalEvaluasi / $totalMahasiswa) * 100) : 0;
?>

<div class="mb-6 grid gap-4 md:grid-cols-3">
    <!-- Stat 1 -->
    <div class="flex items-center gap-4 rounded-2xl border border-violet-100 bg-white p-5 shadow-sm dark:border-violet-900/30 dark:bg-slate-900">
        <div class="grid h-14 w-14 shrink-0 place-items-center rounded-xl bg-violet-100 text-violet-600 dark:bg-violet-900/50 dark:text-violet-400">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-7 w-7"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
        </div>
        <div class="min-w-0 flex-1">
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Evaluasi Masuk</h3>
            <div class="mt-1 flex items-end gap-2">
                <strong class="text-3xl font-extrabold text-slate-900 dark:text-white"><?= (int) ($totalEvaluasi ?? 0) ?></strong>
                <span class="mb-1 text-sm text-slate-500">/ <?= (int) ($totalMahasiswa ?? 0) ?></span>
            </div>
            <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                <div class="h-full rounded-full bg-violet-500 transition-all duration-1000" style="width: <?= $pctEvaluasi ?>%"></div>
            </div>
        </div>
    </div>

    <!-- Stat 2 -->
    <div class="flex items-center gap-4 rounded-2xl border border-amber-100 bg-white p-5 shadow-sm dark:border-amber-900/30 dark:bg-slate-900">
        <div class="grid h-14 w-14 shrink-0 place-items-center rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-900/50 dark:text-amber-400">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-7 w-7"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z"/></svg>
        </div>
        <div class="min-w-0 flex-1">
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Rata-rata Rating</h3>
            <div class="mt-1 flex items-center gap-2">
                <strong class="text-3xl font-extrabold text-slate-900 dark:text-white"><?= $avgRating !== null ? esc((string) $avgRating) : '-' ?></strong>
                <?php if ($avgRating !== null): ?>
                    <span class="text-sm font-bold text-slate-400">/ 5</span>
                <?php endif; ?>
            </div>
            <div class="mt-1.5">
                <?= $stars($avgRating !== null ? (int) round($avgRating) : null) ?>
            </div>
        </div>
    </div>

    <!-- Stat 3 -->
    <div class="flex items-center gap-4 rounded-2xl border border-rose-100 bg-white p-5 shadow-sm dark:border-rose-900/30 dark:bg-slate-900">
        <div class="grid h-14 w-14 shrink-0 place-items-center rounded-xl bg-rose-100 text-rose-600 dark:bg-rose-900/50 dark:text-rose-400">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-7 w-7"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="min-w-0 flex-1">
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Belum Mengisi</h3>
            <div class="mt-1 flex items-end gap-2">
                <strong class="text-3xl font-extrabold text-slate-900 dark:text-white"><?= count($belumEvaluasi ?? []) ?></strong>
                <span class="mb-1 text-sm text-slate-500">Mahasiswa</span>
            </div>
            <p class="mt-1.5 text-xs text-rose-500 font-semibold">Tunggu evaluasi dikirim</p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-head">
        <div>
        <h2>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="inline h-5 w-5 text-violet-500 mr-2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            Evaluasi Mahasiswa Bimbingan
        </h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Isi atau perbarui penilaian mahasiswa yang berada di kelompok Anda.</p>
        </div>
    </div>
    <div class="table-wrap responsive-table">
        <table class="data w-full text-left">
            <thead>
                <tr>
                    <th>Mahasiswa</th>
                    <th>Kelompok</th>
                    <th>Rating Umum</th>
                    <th>Bimbingan</th>
                    <th>Lokasi</th>
                    <th>Pelaksanaan</th>
                    <th>Kategori</th>
                    <th>Komentar</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
            <?php if (empty($evaluasi)): ?>
                <tr>
                    <td colspan="10">
                        <div class="flex flex-col items-center justify-center py-14 px-6 text-center">
                            <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-2xl bg-violet-50 text-violet-300 dark:bg-violet-900/20 dark:text-violet-700">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" class="h-8 w-8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Belum ada evaluasi</p>
                            <p class="mt-1 text-xs text-slate-400">Evaluasi yang Anda isi untuk mahasiswa akan muncul di sini</p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($evaluasi as $row): ?>
                    <?php 
                        $avg = (int)$row['rating']; 
                        $rowClass = '';
                        if ($avg >= 5) $rowClass = 'bg-emerald-50/30 dark:bg-emerald-900/10';
                        elseif ($avg <= 2) $rowClass = 'bg-rose-50/50 dark:bg-rose-900/20';
                    ?>
                    <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/50 <?= $rowClass ?>">
                        <td data-label="Mahasiswa">
                            <strong class="text-slate-900 dark:text-white"><?= esc($row['nama_mahasiswa'] ?? '-') ?></strong><br>
                            <span class="font-mono text-xs text-slate-500"><?= esc($row['npm'] ?? '') ?></span>
                        </td>
                        <td data-label="Kelompok" class="text-xs font-semibold"><?= esc($row['nama_kelompok'] ?? '-') ?></td>
                        <td data-label="Rating">
                            <div class="evaluasi-rating rounded-lg bg-white/50 p-1.5 shadow-sm dark:bg-slate-900/50">
                                <?= $stars((int) $row['rating']) ?>
                                <strong class="ml-1 font-mono text-sm <?= $avg >= 4 ? 'text-emerald-600' : ($avg <= 2 ? 'text-rose-600' : 'text-amber-600') ?>"><?= (int) $row['rating'] ?></strong>
                            </div>
                        </td>
                        <td data-label="Bimbingan"><?= $stars((int) ($row['aspek_bimbingan'] ?? 0)) ?></td>
                        <td data-label="Lokasi"><?= $stars((int) ($row['aspek_lokasi'] ?? 0)) ?></td>
                        <td data-label="Pelaksanaan"><?= $stars((int) ($row['aspek_pelaksanaan'] ?? 0)) ?></td>
                        <td data-label="Kategori">
                            <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-[10px] font-bold text-blue-700 ring-1 ring-inset ring-blue-700/10 dark:bg-blue-900/30 dark:text-blue-400 dark:ring-blue-900">
                                <?= esc($row['kategori'] ?? 'Umum') ?>
                            </span>
                        </td>
                        <td data-label="Komentar" class="max-w-[200px]">
                            <p class="truncate text-xs italic text-slate-600 dark:text-slate-400" title="<?= esc($row['komentar'] ?? '') ?>">
                                "<?= esc(mb_strimwidth($row['komentar'] ?? '-', 0, 60, '…')) ?>"
                            </p>
                        </td>
                        <td data-label="Tanggal" class="text-xs whitespace-nowrap text-slate-500"><?= format_tanggal($row['created_at'] ?? null) ?></td>
                        <td data-label="Aksi"><a href="<?= site_url('dpl/evaluasi/' . (int) $row['mahasiswa_id']) ?>" class="btn btn-secondary btn-sm">Ubah</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (! empty($belumEvaluasi)): ?>
<div class="card overflow-hidden border-rose-200 shadow-sm shadow-rose-900/5 dark:border-rose-900/50">
    <div class="card-head border-b border-rose-100 bg-rose-50 px-5 py-4 dark:border-rose-900/50 dark:bg-rose-950/20 -mx-5 -mt-5 mb-5">
        <h2 class="text-rose-800 dark:text-rose-300">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="inline h-5 w-5 mr-2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Belum Mengirim Evaluasi (<?= count($belumEvaluasi) ?> Mahasiswa)
        </h2>
    </div>
    <div class="table-wrap responsive-table">
        <table class="data w-full text-left">
            <thead>
                <tr>
                    <th>NPM</th>
                    <th>Nama Mahasiswa</th>
                    <th>Program Studi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                <?php foreach ($belumEvaluasi as $row): ?>
                    <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <td data-label="NPM" class="font-mono text-sm text-slate-600 dark:text-slate-400"><?= esc($row['npm']) ?></td>
                        <td data-label="Nama" class="font-bold text-slate-900 dark:text-white"><?= esc($row['nama']) ?></td>
                        <td data-label="Prodi" class="text-sm text-slate-500"><?= esc($row['prodi'] ?? '-') ?></td>
                        <td data-label="Aksi"><a href="<?= site_url('dpl/evaluasi/' . (int) $row['id']) ?>" class="btn btn-primary btn-sm">Beri evaluasi</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
