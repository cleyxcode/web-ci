<?php
$stars = static function (float|int|null $value): string {
    $rating = (int) round((float) ($value ?? 0));
    $html = '<span class="evaluasi-rating-stars" aria-label="Rating ' . $rating . ' dari 5">';
    for ($i = 1; $i <= 5; $i++) $html .= '<svg viewBox="0 0 24 24" class="star-icon h-4 w-4 ' . ($i <= $rating ? 'filled' : '') . '"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14l6.91-1.01L12 2z"/></svg>';
    return $html . '</span>';
};
?>
<div class="space-y-6">
    <section class="hero-strip"><div><p class="periode">Monitoring evaluasi</p><h2>Evaluasi Mahasiswa dari DPL</h2><span>Admin hanya memiliki akses baca. Form dan pengisian dilakukan oleh DPL sesuai kelompok bimbingannya.</span></div></section>
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="stat"><div class="label">Evaluasi diterima</div><div class="value"><?= (int) ($totalEvaluasi ?? 0) ?></div><small>hasil dari DPL</small></div>
        <div class="stat"><div class="label">Rata-rata rating</div><div class="value"><?= $avgRating !== null ? esc(number_format((float) $avgRating, 2)) : '-' ?></div><small>skala 1–5</small></div>
        <div class="stat"><div class="label">Mahasiswa</div><div class="value"><?= (int) ($totalMahasiswa ?? 0) ?></div><small>terdaftar</small></div>
    </div>
    <section class="card">
        <div class="card-head"><div><h2>Daftar evaluasi DPL</h2><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Data ini otomatis muncul setelah DPL menyimpan evaluasi.</p></div><span class="stempel stempel-divalidasi">Read-only</span></div>
        <div class="table-wrap responsive-table"><table class="data w-full text-left"><thead><tr><th>Mahasiswa</th><th>Kelompok</th><th>DPL</th><th>Rating</th><th>Catatan</th><th>Terakhir diperbarui</th></tr></thead><tbody>
        <?php if (empty($evaluasi)): ?><tr><td colspan="6"><div class="empty">Belum ada evaluasi yang dikirim DPL.</div></td></tr><?php else: foreach ($evaluasi as $row): ?><tr>
            <td data-label="Mahasiswa"><strong><?= esc($row['nama_mahasiswa'] ?? '-') ?></strong><small class="block text-xs text-slate-400"><?= esc($row['npm'] ?? '-') ?></small></td>
            <td data-label="Kelompok"><?= esc($row['nama_kelompok'] ?? '-') ?></td><td data-label="DPL"><?= esc($row['nama_penilai'] ?? $row['nama_dpl'] ?? '-') ?></td>
            <td data-label="Rating"><?= $stars($row['rating'] ?? 0) ?> <strong><?= esc((string) ($row['rating'] ?? 0)) ?>/5</strong></td>
            <td data-label="Catatan" class="max-w-xs"><span class="block truncate" title="<?= esc($row['komentar'] ?? '', 'attr') ?>"><?= esc(mb_strimwidth($row['komentar'] ?? '-', 0, 80, '…')) ?></span></td>
            <td data-label="Tanggal"><?= format_tanggal($row['updated_at'] ?? $row['created_at'] ?? null) ?></td>
        </tr><?php endforeach; endif; ?></tbody></table></div>
    </section>
</div>
