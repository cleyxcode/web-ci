<?php
$e = $evaluasi ?? null;
$isEdit = (bool) ($isEdit ?? false);
$selectedMahasiswaId = (int) old('mahasiswa_id', $e['mahasiswa_id'] ?? 0);
$selectedMahasiswa = null;
$defaultCriteria = [
    'Kehadiran dan kedisiplinan',
    'Keaktifan serta tanggung jawab',
    'Kerja sama tim dan komunikasi',
    'Pelaksanaan program kerja',
    'Kualitas logbook dan laporan',
    'Etika/sikap di lokasi KKN',
];
$storedCriteria = [];
if (! empty($e['detail_evaluasi'])) {
    $decodedCriteria = json_decode((string) $e['detail_evaluasi'], true);
    $storedCriteria = is_array($decodedCriteria) ? $decodedCriteria : [];
}
$oldCriteriaNames = old('criteria_nama', null, false);
$oldCriteriaRatings = old('criteria_rating', null, false);
$criteriaRows = [];
if (is_array($oldCriteriaNames)) {
    foreach ($oldCriteriaNames as $index => $name) {
        $criteriaRows[] = ['nama' => (string) $name, 'rating' => (int) ($oldCriteriaRatings[$index] ?? 0)];
    }
} elseif ($storedCriteria !== []) {
    $criteriaRows = $storedCriteria;
} else {
    $criteriaRows = array_map(static fn (string $name): array => ['nama' => $name, 'rating' => 0], $defaultCriteria);
}

foreach ($mahasiswa as $row) {
    if ((int) $row['id'] === $selectedMahasiswaId) {
        $selectedMahasiswa = $row;
        break;
    }
}
?>

<section class="card max-w-3xl">
    <div class="card-head">
        <div>
            <h2><?= $isEdit ? 'Perbarui evaluasi mahasiswa' : 'Buat evaluasi mahasiswa' ?></h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Pilih mahasiswa, beri rating 1–5 bintang, lalu tulis umpan balik atau arahan revisi.</p>
        </div>
        <a href="<?= site_url('admin/evaluasi') ?>" class="btn btn-secondary btn-sm">Kembali</a>
    </div>

    <?php if (! empty(session('errors'))): ?>
        <div class="alert alert-danger mb-5">
            <strong>Periksa kembali isian berikut:</strong>
            <ul class="mt-1 list-inside list-disc">
                <?php foreach ((array) session('errors') as $error): ?>
                    <li><?= esc(is_array($error) ? implode(', ', $error) : $error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= $isEdit ? site_url('admin/evaluasi/' . $e['id']) : site_url('admin/evaluasi') ?>" data-star-rating-form>
        <?= csrf_field() ?>

        <div class="field">
            <label for="mahasiswa_id">Mahasiswa</label>
            <?php if ($isEdit): ?>
                <input type="hidden" name="mahasiswa_id" value="<?= $selectedMahasiswaId ?>">
                <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-800 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    <?= esc($selectedMahasiswa['nama'] ?? 'Mahasiswa') ?> <span class="font-mono text-xs text-slate-500 dark:text-slate-400">· <?= esc($selectedMahasiswa['npm'] ?? '-') ?></span>
                </div>
            <?php else: ?>
                <select id="mahasiswa_id" name="mahasiswa_id" required>
                    <option value="">Pilih mahasiswa yang akan dievaluasi</option>
                    <?php foreach ($mahasiswa as $row): ?>
                        <option value="<?= (int) $row['id'] ?>" <?= $selectedMahasiswaId === (int) $row['id'] ? 'selected' : '' ?>>
                            <?= esc($row['nama']) ?> · <?= esc($row['npm']) ?><?= ! empty($row['nama_kelompok']) ? ' — ' . esc($row['nama_kelompok']) : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </div>

        <fieldset class="mb-6 rounded-2xl border border-slate-200 bg-slate-50 p-5 text-center dark:border-slate-800 dark:bg-slate-800/50" data-star-rating>
            <legend class="mx-auto px-2 text-base font-extrabold text-slate-900 dark:text-white">Rating evaluasi</legend>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Pilih bintang untuk menentukan hasil evaluasi.</p>
            <div class="mt-4 flex justify-center gap-1 sm:gap-2" role="radiogroup" aria-label="Rating evaluasi">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <label class="star-label cursor-pointer rounded-xl p-2 transition hover:bg-white hover:shadow-sm dark:hover:bg-slate-700" data-value="<?= $i ?>">
                        <input class="sr-only" type="radio" name="rating" value="<?= $i ?>" <?= (string) old('rating', $e['rating'] ?? '') === (string) $i ? 'checked' : '' ?> required>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="star-icon h-10 w-10 sm:h-12 sm:w-12" aria-hidden="true"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z"/></svg>
                        <span class="sr-only"><?= $i ?> bintang</span>
                    </label>
                <?php endfor; ?>
            </div>
            <p class="mt-3 min-h-6 text-sm font-extrabold" data-rating-label aria-live="polite"></p>
            <input type="hidden" name="kategori" data-rating-category value="<?= esc(old('kategori', $e['kategori'] ?? '')) ?>">
        </fieldset>

        <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900" aria-labelledby="criteria-title">
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h3 id="criteria-title" class="text-base font-extrabold text-slate-900 dark:text-white">Kriteria penilaian</h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Gunakan kriteria bawaan atau tambahkan aspek lain yang relevan dengan kondisi lapangan.</p>
                </div>
                <button type="button" class="btn btn-secondary btn-sm shrink-0" data-add-criterion>+ Tambah kriteria</button>
            </div>

            <div class="space-y-3" data-criteria-list>
                <?php foreach ($criteriaRows as $criterion): ?>
                    <?php $criterionRating = (int) ($criterion['rating'] ?? 0); ?>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-800 dark:bg-slate-800/50" data-criterion>
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                            <div class="min-w-0 flex-1">
                                <label class="sr-only">Nama kriteria</label>
                                <input type="text" name="criteria_nama[]" value="<?= esc($criterion['nama'] ?? '') ?>" maxlength="100" required placeholder="Contoh: Inisiatif di lapangan">
                            </div>
                            <div class="flex items-center gap-2" data-criterion-stars aria-label="Rating kriteria">
                                <div class="flex gap-0.5">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <button type="button" class="criterion-star <?= $i <= $criterionRating ? 'is-selected' : '' ?>" data-criterion-value="<?= $i ?>" aria-label="<?= $i ?> bintang">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z"/></svg>
                                        </button>
                                    <?php endfor; ?>
                                </div>
                                <input type="hidden" name="criteria_rating[]" value="<?= $criterionRating ?: '' ?>" required data-criterion-rating>
                                <span class="w-24 text-xs font-bold text-slate-500 dark:text-slate-400" data-criterion-label><?= $criterionRating > 0 ? esc($criterionRating . '/5') : 'Pilih rating' ?></span>
                            </div>
                            <button type="button" class="grid h-10 w-10 shrink-0 place-items-center rounded-xl text-slate-400 transition hover:bg-rose-100 hover:text-rose-600 dark:hover:bg-rose-950/40" data-remove-criterion aria-label="Hapus kriteria">&times;</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <div class="field">
            <label for="komentar">Catatan evaluasi atau arahan revisi</label>
            <span class="field-hint">Sampaikan apa yang sudah sangat baik atau bagian yang perlu diperbaiki dengan jelas dan membangun.</span>
            <textarea id="komentar" name="komentar" rows="6" maxlength="2000" required placeholder="Contoh: Aktivitas lapangan sudah sangat baik. Lengkapi dokumentasi kegiatan minggu terakhir sebelum penilaian final."><?= esc(old('komentar', $e['komentar'] ?? '')) ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Simpan perubahan' : 'Simpan evaluasi' ?></button>
            <a href="<?= site_url('admin/evaluasi') ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('[data-star-rating-form]');
    const ratingBox = form?.querySelector('[data-star-rating]');
    if (!form || !ratingBox) return;

    const labels = ['', 'Perlu Revisi', 'Perlu Perbaikan', 'Cukup', 'Baik', 'Sangat Baik'];
    const colors = ['', 'text-rose-600 dark:text-rose-300', 'text-orange-600 dark:text-orange-300', 'text-amber-600 dark:text-amber-300', 'text-sky-600 dark:text-sky-300', 'text-emerald-600 dark:text-emerald-300'];
    const label = ratingBox.querySelector('[data-rating-label]');
    const category = ratingBox.querySelector('[data-rating-category]');
    const stars = [...ratingBox.querySelectorAll('.star-label')];

    const render = (value) => {
        stars.forEach((star) => {
            const icon = star.querySelector('.star-icon');
            const isFilled = Number(star.dataset.value) <= value;
            icon?.classList.toggle('filled', isFilled);
        });
        if (label) label.className = `mt-3 min-h-6 text-sm font-extrabold ${colors[value] || ''}`;
        if (label) label.textContent = value ? `${value}/5 — ${labels[value]}` : 'Pilih rating 1–5 bintang';
        if (category) category.value = labels[value] || '';
    };

    const checked = ratingBox.querySelector('input[name="rating"]:checked');
    render(checked ? Number(checked.value) : 0);
    stars.forEach((star) => star.addEventListener('change', (event) => render(Number(event.target.value))));

    const criteriaList = form.querySelector('[data-criteria-list]');
    const addCriterion = form.querySelector('[data-add-criterion]');
    const criteriaLabels = ['', 'Perlu Revisi', 'Perlu Perbaikan', 'Cukup', 'Baik', 'Sangat Baik'];

    const paintCriterion = (row, value) => {
        row.querySelectorAll('[data-criterion-value]').forEach((button) => {
            button.classList.toggle('is-selected', Number(button.dataset.criterionValue) <= value);
        });
        const ratingInput = row.querySelector('[data-criterion-rating]');
        const ratingLabel = row.querySelector('[data-criterion-label]');
        if (ratingInput) ratingInput.value = value || '';
        if (ratingLabel) ratingLabel.textContent = value ? `${value}/5 · ${criteriaLabels[value]}` : 'Pilih rating';
    };

    const bindCriterion = (row) => {
        row.querySelectorAll('[data-criterion-value]').forEach((button) => {
            button.addEventListener('click', () => paintCriterion(row, Number(button.dataset.criterionValue)));
        });
        row.querySelector('[data-remove-criterion]')?.addEventListener('click', () => {
            if (criteriaList.querySelectorAll('[data-criterion]').length > 1) row.remove();
        });
    };

    criteriaList?.querySelectorAll('[data-criterion]').forEach((row) => bindCriterion(row));
    addCriterion?.addEventListener('click', () => {
        const row = document.createElement('div');
        row.className = 'rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-800 dark:bg-slate-800/50';
        row.setAttribute('data-criterion', '');
        row.innerHTML = `<div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="min-w-0 flex-1"><label class="sr-only">Nama kriteria</label><input type="text" name="criteria_nama[]" maxlength="100" required placeholder="Contoh: Inisiatif di lapangan"></div>
            <div class="flex items-center gap-2" data-criterion-stars aria-label="Rating kriteria"><div class="flex gap-0.5">
                ${[1, 2, 3, 4, 5].map((value) => `<button type="button" class="criterion-star" data-criterion-value="${value}" aria-label="${value} bintang"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z"/></svg></button>`).join('')}
            </div><input type="hidden" name="criteria_rating[]" value="" required data-criterion-rating><span class="w-24 text-xs font-bold text-slate-500 dark:text-slate-400" data-criterion-label>Pilih rating</span></div>
            <button type="button" class="grid h-10 w-10 shrink-0 place-items-center rounded-xl text-slate-400 transition hover:bg-rose-100 hover:text-rose-600 dark:hover:bg-rose-950/40" data-remove-criterion aria-label="Hapus kriteria">&times;</button>
        </div>`;
        criteriaList?.appendChild(row);
        bindCriterion(row);
        row.querySelector('input[name="criteria_nama[]"]')?.focus();
    });
});
</script>
