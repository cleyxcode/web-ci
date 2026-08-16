<?php

$student = $mahasiswa ?? [];
$evaluation = $evaluasi ?? null;
$details = [];

if (! empty($evaluation['detail_evaluasi'])) {
    $decoded = json_decode((string) $evaluation['detail_evaluasi'], true);
    $details = is_array($decoded) ? $decoded : [];
}

$stars = static function (float|int|null $value, string $size = 'h-5 w-5'): string {
    $rating = (int) round((float) ($value ?? 0));
    $html = '<span class="evaluasi-rating-stars" aria-label="Rating ' . $rating . ' dari 5">';
    for ($i = 1; $i <= 5; $i++) {
        $html .= '<svg viewBox="0 0 24 24" class="star-icon ' . $size . ' ' . ($i <= $rating ? 'filled' : '') . '"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14l6.91-1.01L12 2z"/></svg>';
    }
    return $html . '</span>';
};
?>

<?php if (empty($student['kelompok_id'])): ?>
    <section class="mx-auto max-w-2xl py-20 text-center">
        <div class="mx-auto grid h-20 w-20 place-items-center rounded-3xl bg-amber-50 text-amber-500 dark:bg-amber-950/30"><span class="text-3xl">!</span></div>
        <h1 class="mt-6 text-2xl font-extrabold text-slate-900 dark:text-white">Belum ada kelompok KKN</h1>
        <p class="mx-auto mt-3 max-w-md text-sm leading-6 text-slate-500">Evaluasi DPL akan muncul setelah admin menempatkan Anda ke kelompok KKN.</p>
    </section>
<?php elseif (! $evaluation): ?>
    <section class="mx-auto max-w-2xl rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <div class="mx-auto grid h-20 w-20 place-items-center rounded-3xl bg-emerald-50 text-emerald-500 dark:bg-emerald-950/30"><span class="text-3xl">★</span></div>
        <h1 class="mt-6 text-2xl font-extrabold text-slate-900 dark:text-white">Evaluasi belum tersedia</h1>
        <p class="mx-auto mt-3 max-w-md text-sm leading-6 text-slate-500">DPL <?= esc($student['nama_dpl'] ?? 'kelompok Anda') ?> belum mengisi evaluasi. Anda akan dapat melihat rincian penilaian di halaman ini setelah disimpan.</p>
    </section>
<?php else: ?>
    <div class="mx-auto max-w-5xl space-y-6">
        <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-700 via-cyan-700 to-emerald-700 p-6 text-white shadow-xl sm:p-8">
            <div class="absolute -right-20 -top-24 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
            <div class="relative z-10 flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-[0.2em] text-cyan-100">Evaluasi DPL</p>
                    <h1 class="mt-2 text-3xl font-extrabold tracking-tight">Hasil evaluasi Anda</h1>
                    <p class="mt-3 text-sm text-cyan-50"><?= esc($student['nama_kelompok'] ?? '-') ?> · DPL <?= esc($student['nama_dpl'] ?? '-') ?></p>
                </div>
                <div class="rounded-2xl bg-white/10 px-6 py-4 text-center ring-1 ring-white/20 backdrop-blur">
                    <p class="text-[11px] font-extrabold uppercase tracking-widest text-cyan-100">Skor rata-rata</p>
                    <p class="mt-1 text-4xl font-extrabold"><?= esc(number_format((float) ($evaluation['rating'] ?? 0), 2)) ?><span class="text-lg text-cyan-100">/5</span></p>
                    <div class="mt-2"><?= $stars($evaluation['rating'] ?? 0, 'h-4 w-4') ?></div>
                </div>
            </div>
        </section>

        <section class="card">
            <div class="card-head">
                <div>
                    <h2>Rincian penilaian</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Setiap kriteria dinilai langsung oleh DPL Anda.</p>
                </div>
                <span class="text-xs font-semibold text-slate-400"><?= format_tanggal($evaluation['updated_at'] ?? $evaluation['created_at'] ?? null) ?></span>
            </div>
            <?php if ($details === []): ?>
                <p class="text-sm text-slate-500">Rincian kriteria belum tersedia.</p>
            <?php else: ?>
                <div class="grid gap-3 sm:grid-cols-2">
                    <?php foreach ($details as $detail): ?>
                        <?php $rating = (int) ($detail['rating'] ?? 0); ?>
                        <article class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 dark:border-slate-800 dark:bg-slate-800/40">
                            <div class="flex items-start justify-between gap-4">
                                <div><h3 class="text-sm font-extrabold text-slate-900 dark:text-white"><?= esc($detail['nama'] ?? '-') ?></h3><?php if (! empty($detail['deskripsi'])): ?><p class="mt-1 text-xs text-slate-500"><?= esc($detail['deskripsi']) ?></p><?php endif; ?></div>
                                <strong class="shrink-0 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-extrabold text-amber-700 dark:bg-amber-950/40 dark:text-amber-300"><?= $rating ?>/5</strong>
                            </div>
                            <div class="mt-3"><?= $stars($rating, 'h-4 w-4') ?></div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="card border-amber-200 bg-amber-50/60 dark:border-amber-900/50 dark:bg-amber-950/15">
            <p class="text-xs font-extrabold uppercase tracking-widest text-amber-700 dark:text-amber-300">Catatan pembimbing</p>
            <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-700 dark:text-slate-200"><?= esc($evaluation['komentar'] ?: 'DPL tidak menambahkan catatan.') ?></p>
        </section>
    </div>
<?php endif; ?>
