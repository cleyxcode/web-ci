<?php
$ratingLabel = static function (int $rating): string {
    return match ($rating) {
        5 => 'Sangat Baik',
        4 => 'Baik',
        3 => 'Cukup',
        2 => 'Perlu Perbaikan',
        default => 'Perlu Revisi',
    };
};

$stars = static function (int $rating): string {
    $html = '<span class="evaluasi-rating-stars" aria-label="Rating ' . $rating . ' dari 5">';
    for ($i = 1; $i <= 5; $i++) {
        $html .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="star-icon h-4 w-4 shrink-0 ' . ($i <= $rating ? 'filled' : '') . '" aria-hidden="true"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z"/></svg>';
    }

    return $html . '</span>';
};
?>

<div class="stat-row">
    <div class="stat"><div class="label">Total evaluasi</div><div class="value js-count-up" data-count-up="<?= (int) ($totalEvaluasi ?? 0) ?>"><?= (int) ($totalEvaluasi ?? 0) ?></div><small>semua evaluasi tercatat</small></div>
    <div class="stat"><div class="label">Rata-rata rating</div><div class="value<?= $avgRating !== null ? ' js-count-up' : '' ?>"<?= $avgRating !== null ? ' data-count-up="' . esc((string) $avgRating, 'attr') . '" data-count-decimals="2"' : '' ?>><?= $avgRating !== null ? esc((string) $avgRating) : '-' ?></div><small>skala 1–5</small></div>
    <div class="stat"><div class="label">Partisipasi mahasiswa</div><div class="value js-count-up" data-count-up="<?= (int) (($totalMahasiswa ?? 0) > 0 ? round(((int) ($totalEvaluasi ?? 0) / (int) $totalMahasiswa) * 100) : 0) ?>" data-count-suffix="%">0%</div><small>dari <?= (int) ($totalMahasiswa ?? 0) ?> mahasiswa</small></div>
</div>

<section class="card">
    <div class="card-head">
        <div>
            <h2>Evaluasi mahasiswa</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Buat evaluasi dengan rating bintang dan catatan arahan/revisi untuk mahasiswa.</p>
        </div>
        <a href="<?= site_url('admin/evaluasi/create') ?>" class="btn btn-primary">Buat evaluasi</a>
    </div>

    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>Mahasiswa</th>
                    <th>Kelompok</th>
                    <th>Pengirim</th>
                    <th>Rating</th>
                    <th>Kategori</th>
                    <th>Catatan</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($evaluasi)): ?>
                <tr>
                    <td colspan="8">
                        <div class="flex flex-col items-center justify-center py-14 px-6 text-center">
                            <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-2xl bg-violet-50 text-violet-300 dark:bg-violet-900/20 dark:text-violet-700">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" class="h-8 w-8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-5l-5 5v-5z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Belum ada evaluasi</p>
                            <p class="mt-1 text-xs text-slate-400">Klik "Buat evaluasi" untuk memberikan umpan balik kepada mahasiswa</p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($evaluasi as $row): ?>
                    <?php $isAdminEvaluation = ($row['tipe_evaluasi'] ?? 'mahasiswa') === 'admin'; ?>
                    <tr>
                        <td data-label="Mahasiswa"><strong class="text-slate-900 dark:text-white"><?= esc($row['nama_mahasiswa'] ?? '-') ?></strong><small class="mt-0.5 block font-mono text-xs text-slate-400"><?= esc($row['npm'] ?? '-') ?></small></td>
                        <td data-label="Kelompok"><?= esc($row['nama_kelompok'] ?? '-') ?></td>
                        <td data-label="Pengirim"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-extrabold <?= $isAdminEvaluation ? 'bg-violet-100 text-violet-700 dark:bg-violet-950/50 dark:text-violet-300' : 'bg-sky-100 text-sky-700 dark:bg-sky-950/50 dark:text-sky-300' ?>"><?= $isAdminEvaluation ? 'Admin' : 'Mahasiswa' ?></span></td>
                        <td data-label="Rating"><span class="evaluasi-rating"><?= $stars((int) ($row['rating'] ?? 0)) ?><span class="font-mono text-xs text-slate-500 dark:text-slate-400"><?= (int) ($row['rating'] ?? 0) ?>/5</span></span></td>
                        <td data-label="Kategori"><span class="font-semibold text-slate-700 dark:text-slate-200"><?= esc($row['kategori'] ?: $ratingLabel((int) ($row['rating'] ?? 1))) ?></span></td>
                        <td data-label="Catatan" class="max-w-xs"><span class="block truncate" title="<?= esc($row['komentar'] ?? '', 'attr') ?>"><?= esc(mb_strimwidth($row['komentar'] ?? '-', 0, 80, '…')) ?></span></td>
                        <td data-label="Tanggal"><?= format_tanggal($row['updated_at'] ?? $row['created_at'] ?? null) ?></td>
                        <td data-label="Aksi">
                            <div class="actions">
                                <?php if ($isAdminEvaluation): ?>
                                    <a href="<?= site_url('admin/evaluasi/' . $row['id'] . '/edit') ?>" class="btn btn-secondary btn-sm">Ubah</a>
                                <?php endif; ?>
                                <form method="post" action="<?= site_url('admin/evaluasi/' . $row['id'] . '/delete') ?>" class="inline-form" data-confirm="Hapus evaluasi dari <?= esc($row['nama_mahasiswa'] ?? 'mahasiswa') ?>?">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
