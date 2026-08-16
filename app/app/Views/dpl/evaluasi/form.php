<?php

$student = $mahasiswa ?? [];
$existing = $evaluasi ?? null;
$criteria = $criteria ?? [];
$savedRatings = [];

if (! empty($existing['detail_evaluasi'])) {
    $decoded = json_decode((string) $existing['detail_evaluasi'], true);
    if (is_array($decoded)) {
        foreach ($decoded as $item) {
            if (isset($item['id'])) {
                $savedRatings[(int) $item['id']] = (int) ($item['rating'] ?? 0);
            }
        }
    }
}

$oldRatings = old('criteria_rating', [], false);
if (is_array($oldRatings)) {
    foreach ($oldRatings as $id => $rating) {
        $savedRatings[(int) $id] = (int) $rating;
    }
}

$stars = static function (int $value): string {
    $html = '<span class="evaluasi-rating-stars" aria-hidden="true">';
    for ($i = 1; $i <= 5; $i++) {
        $html .= '<svg viewBox="0 0 24 24" class="star-icon h-4 w-4 ' . ($i <= $value ? 'filled' : '') . '"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z"/></svg>';
    }
    return $html . '</span>';
};
?>

<div class="mx-auto max-w-4xl space-y-6">
    <div class="flex flex-col gap-4 rounded-3xl bg-gradient-to-br from-emerald-700 via-teal-700 to-cyan-800 p-6 text-white shadow-xl sm:p-8 md:flex-row md:items-end md:justify-between">
        <div>
            <a href="<?= site_url('dpl/evaluasi') ?>" class="mb-5 inline-flex items-center gap-2 text-sm font-bold text-emerald-100 transition hover:text-white">← Kembali ke daftar mahasiswa</a>
            <p class="text-xs font-extrabold uppercase tracking-[0.2em] text-emerald-200">Evaluasi DPL</p>
            <h1 class="mt-2 text-2xl font-extrabold tracking-tight sm:text-3xl"><?= esc($student['nama'] ?? '-') ?></h1>
            <p class="mt-2 text-sm text-emerald-100"><?= esc($student['npm'] ?? '-') ?> · <?= esc($student['nama_kelompok'] ?? 'Kelompok belum tersedia') ?></p>
        </div>
        <div class="rounded-2xl bg-white/10 px-5 py-4 text-right ring-1 ring-white/20 backdrop-blur">
            <p class="text-[11px] font-extrabold uppercase tracking-widest text-emerald-200">Status</p>
            <p class="mt-1 text-sm font-bold"><?= $existing ? 'Pembaruan evaluasi' : 'Belum dievaluasi' ?></p>
        </div>
    </div>

    <?php if (! empty(session('errors'))): ?>
        <div class="alert alert-danger">
            <strong>Lengkapi penilaian berikut:</strong>
            <ul class="mt-1 list-inside list-disc">
                <?php foreach ((array) session('errors') as $error): ?>
                    <li><?= esc(is_array($error) ? implode(', ', $error) : $error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($criteria === []): ?>
        <section class="card border-amber-200 bg-amber-50/60 text-center dark:border-amber-900/50 dark:bg-amber-950/20">
            <h2 class="text-lg font-extrabold text-amber-800 dark:text-amber-200">Kriteria belum tersedia</h2>
            <p class="mt-2 text-sm text-amber-700 dark:text-amber-300">Minta admin menambahkan kriteria evaluasi sebelum Anda mengisi formulir.</p>
        </section>
    <?php else: ?>
        <form method="post" action="<?= site_url('dpl/evaluasi/' . (int) $student['id']) ?>" class="space-y-6" data-dpl-evaluation-form>
            <?= csrf_field() ?>

            <section class="card">
                <div class="card-head">
                    <div>
                        <h2>Rincian penilaian</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Nilai setiap kriteria dengan bintang 1 sampai 5. Semua kriteria wajib diisi.</p>
                    </div>
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-extrabold text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">Skala 1–5</span>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <?php foreach ($criteria as $criterion): ?>
                        <?php $criterionId = (int) $criterion['id']; $value = $savedRatings[$criterionId] ?? 0; ?>
                        <fieldset class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 transition hover:border-emerald-300 dark:border-slate-800 dark:bg-slate-800/40" data-rating-card>
                            <legend class="sr-only"><?= esc($criterion['nama']) ?></legend>
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white"><?= esc($criterion['nama']) ?></h3>
                                    <?php if (! empty($criterion['deskripsi'])): ?><p class="mt-1 text-xs leading-5 text-slate-500 dark:text-slate-400"><?= esc($criterion['deskripsi']) ?></p><?php endif; ?>
                                </div>
                                <output class="min-w-10 rounded-full bg-white px-2 py-1 text-center text-xs font-extrabold text-slate-500 shadow-sm dark:bg-slate-900 dark:text-slate-300" data-rating-value><?= $value ? $value . '/5' : '—' ?></output>
                            </div>
                            <div class="mt-4 flex gap-1" role="radiogroup" aria-label="Rating <?= esc($criterion['nama']) ?>">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <label class="rating-star-button cursor-pointer rounded-lg p-1.5 transition hover:bg-white dark:hover:bg-slate-700" data-rating-star="<?= $i ?>">
                                        <input class="sr-only" type="radio" name="criteria_rating[<?= $criterionId ?>]" value="<?= $i ?>" <?= $value === $i ? 'checked' : '' ?> required>
                                        <svg viewBox="0 0 24 24" class="star-icon h-7 w-7 <?= $i <= $value ? 'filled' : '' ?>"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z"/></svg>
                                        <span class="sr-only"><?= $i ?> bintang</span>
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </fieldset>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="card">
                <div class="field">
                    <label for="komentar">Catatan pembimbing</label>
                    <span class="field-hint">Tuliskan perkembangan, kekuatan, dan arahan perbaikan yang dapat ditindaklanjuti mahasiswa.</span>
                    <textarea id="komentar" name="komentar" rows="6" maxlength="2000" placeholder="Contoh: Pertahankan koordinasi dengan masyarakat dan lengkapi dokumentasi program kerja terakhir."><?= esc(old('komentar', $existing['komentar'] ?? '')) ?></textarea>
                </div>
                <div class="form-actions mt-6 border-t border-slate-100 pt-5 dark:border-slate-800">
                    <a href="<?= site_url('dpl/evaluasi') ?>" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan evaluasi</button>
                </div>
            </section>
        </form>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-rating-card]').forEach(function (card) {
        const labels = card.querySelectorAll('[data-rating-star]');
        const output = card.querySelector('[data-rating-value]');
        const render = function (value) {
            labels.forEach(function (label) {
                const star = label.querySelector('.star-icon');
                star.classList.toggle('filled', Number(label.dataset.ratingStar) <= value);
            });
            output.textContent = value > 0 ? value + '/5' : '—';
        };
        labels.forEach(function (label) {
            label.addEventListener('mouseenter', function () { render(Number(label.dataset.ratingStar)); });
            label.addEventListener('mouseleave', function () {
                const checked = card.querySelector('input:checked');
                render(checked ? Number(checked.value) : 0);
            });
            label.addEventListener('click', function () { render(Number(label.dataset.ratingStar)); });
        });
    });
});
</script>
