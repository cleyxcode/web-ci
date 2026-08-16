<?php

$stars = static function (float|int|null $value, string $size = 'h-4 w-4'): string {
    $rating = (int) round((float) ($value ?? 0));
    $html = '<span class="evaluasi-rating-stars" aria-label="Rating ' . $rating . ' dari 5">';
    for ($i = 1; $i <= 5; $i++) {
        $html .= '<svg viewBox="0 0 24 24" class="star-icon ' . $size . ' ' . ($i <= $rating ? 'filled' : '') . '"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14l6.91-1.01L12 2z"/></svg>';
    }
    return $html . '</span>';
};
?>

<div class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="stat"><div class="label">Evaluasi DPL</div><div class="value js-count-up" data-count-up="<?= (int) ($totalEvaluasi ?? 0) ?>"><?= (int) ($totalEvaluasi ?? 0) ?></div><small>hasil tersimpan</small></div>
        <div class="stat"><div class="label">Rata-rata</div><div class="value"><?= $avgRating !== null ? esc(number_format((float) $avgRating, 2)) : '-' ?></div><small>skala 1–5</small></div>
        <div class="stat"><div class="label">Mahasiswa</div><div class="value js-count-up" data-count-up="<?= (int) ($totalMahasiswa ?? 0) ?>"><?= (int) ($totalMahasiswa ?? 0) ?></div><small>terdaftar</small></div>
        <div class="stat"><div class="label">Kriteria aktif</div><div class="value js-count-up" data-count-up="<?= count(array_filter($criteria ?? [], static fn (array $row): bool => (int) ($row['aktif'] ?? 0) === 1)) ?>"><?= count(array_filter($criteria ?? [], static fn (array $row): bool => (int) ($row['aktif'] ?? 0) === 1)) ?></div><small>dipakai DPL</small></div>
    </div>

    <section class="card border-violet-200 dark:border-violet-900/50">
        <div class="card-head">
            <div>
                <h2>Template kriteria evaluasi</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Admin menentukan pertanyaan penilaian. DPL akan melihat kriteria aktif dan memberi rating bintang 1–5.</p>
            </div>
            <span class="rounded-full bg-violet-50 px-3 py-1 text-xs font-extrabold text-violet-700 dark:bg-violet-950/40 dark:text-violet-300">Admin only</span>
        </div>

        <form method="post" action="<?= site_url('admin/evaluasi/kriteria') ?>" class="mb-5 grid gap-3 rounded-2xl bg-violet-50/60 p-4 dark:bg-violet-950/20 md:grid-cols-[1fr_1fr_auto_auto] md:items-end">
            <?= csrf_field() ?>
            <div class="field"><label for="new-nama">Nama kriteria</label><input id="new-nama" name="nama" maxlength="150" required placeholder="Contoh: Kedisiplinan dan kehadiran"></div>
            <div class="field"><label for="new-deskripsi">Petunjuk singkat</label><input id="new-deskripsi" name="deskripsi" maxlength="255" placeholder="Apa yang perlu diamati DPL?"></div>
            <label class="mb-2 inline-flex items-center gap-2 text-sm font-bold text-slate-700 dark:text-slate-200"><input type="checkbox" name="aktif" value="1" checked> Aktif</label>
            <button class="btn btn-primary" type="submit">+ Tambah</button>
        </form>

        <?php if (empty($criteria)): ?>
            <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500 dark:border-slate-700">Belum ada kriteria. Tambahkan minimal satu kriteria agar DPL dapat menilai.</div>
        <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($criteria as $criterion): ?>
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900">
                        <form method="post" action="<?= site_url('admin/evaluasi/kriteria/' . (int) $criterion['id']) ?>" class="grid gap-3 md:grid-cols-[1fr_1fr_76px_auto_auto] md:items-end">
                            <?= csrf_field() ?>
                            <div class="field"><label>Nama</label><input name="nama" maxlength="150" value="<?= esc($criterion['nama']) ?>" required></div>
                            <div class="field"><label>Petunjuk</label><input name="deskripsi" maxlength="255" value="<?= esc($criterion['deskripsi'] ?? '') ?>" placeholder="Opsional"></div>
                            <div class="field"><label>Urutan</label><input type="number" name="urutan" min="1" value="<?= (int) $criterion['urutan'] ?>"></div>
                            <label class="mb-2 inline-flex items-center gap-2 text-sm font-bold text-slate-700 dark:text-slate-200"><input type="checkbox" name="aktif" value="1" <?= (int) $criterion['aktif'] === 1 ? 'checked' : '' ?>> Aktif</label>
                            <button class="btn btn-secondary" type="submit">Simpan</button>
                        </form>
                        <form method="post" action="<?= site_url('admin/evaluasi/kriteria/' . (int) $criterion['id'] . '/delete') ?>" class="mt-2 text-right" data-confirm="Hapus kriteria ini? Riwayat evaluasi tetap aman karena nama kriteria tersimpan di detail evaluasi.">
                            <?= csrf_field() ?><button class="text-xs font-bold text-rose-600 hover:text-rose-700" type="submit">Hapus kriteria</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <section class="card">
        <div class="card-head">
            <div>
                <h2>Hasil evaluasi DPL</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Semua hasil dari seluruh kelompok dapat dipantau di sini.</p>
            </div>
        </div>
        <div class="table-wrap responsive-table">
            <table class="data w-full text-left">
                <thead><tr><th>Mahasiswa</th><th>Kelompok</th><th>DPL</th><th>Rating</th><th>Rincian kriteria</th><th>Catatan</th><th>Tanggal</th></tr></thead>
                <tbody>
                <?php if (empty($evaluasi)): ?>
                    <tr><td colspan="7"><div class="py-14 text-center text-sm text-slate-500">Belum ada evaluasi dari DPL.</div></td></tr>
                <?php else: foreach ($evaluasi as $row): ?>
                    <?php $details = json_decode((string) ($row['detail_evaluasi'] ?? '[]'), true); $details = is_array($details) ? $details : []; ?>
                    <tr>
                        <td data-label="Mahasiswa"><strong><?= esc($row['nama_mahasiswa'] ?? '-') ?></strong><small class="mt-0.5 block font-mono text-xs text-slate-400"><?= esc($row['npm'] ?? '-') ?></small></td>
                        <td data-label="Kelompok"><?= esc($row['nama_kelompok'] ?? '-') ?></td>
                        <td data-label="DPL"><?= esc($row['nama_penilai'] ?? $row['nama_dpl'] ?? '-') ?></td>
                        <td data-label="Rating"><span class="evaluasi-rating"><?= $stars($row['rating'] ?? 0) ?><strong class="ml-1 font-mono text-xs"><?= esc((string) ($row['rating'] ?? 0)) ?>/5</strong></span></td>
                        <td data-label="Kriteria"><div class="max-w-xs space-y-1"><?php foreach ($details as $detail): ?><div class="flex items-center justify-between gap-3 text-xs"><span class="truncate"><?= esc($detail['nama'] ?? '-') ?></span><strong class="text-amber-600"><?= (int) ($detail['rating'] ?? 0) ?>/5</strong></div><?php endforeach; ?></div></td>
                        <td data-label="Catatan" class="max-w-xs"><span class="block truncate" title="<?= esc($row['komentar'] ?? '', 'attr') ?>"><?= esc(mb_strimwidth($row['komentar'] ?? '-', 0, 70, '…')) ?></span></td>
                        <td data-label="Tanggal" class="whitespace-nowrap text-xs"><?= format_tanggal($row['updated_at'] ?? $row['created_at'] ?? null) ?></td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
