<?php
$e = $evaluasi ?? null;
$m = $mahasiswa ?? [];
$adminCriteria = [];
if (! empty($evaluasiAdmin['detail_evaluasi'])) {
    $decodedAdminCriteria = json_decode((string) $evaluasiAdmin['detail_evaluasi'], true);
    $adminCriteria = is_array($decodedAdminCriteria) ? $decodedAdminCriteria : [];
}
$stars = static function (int $value, string $sizeClass = 'h-5 w-5'): string {
    $html = '<div class="evaluasi-rating-stars">';
    for ($i = 1; $i <= 5; $i++) {
        $html .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="star-icon shrink-0 ' . $sizeClass . ' ' . ($i <= $value ? 'filled' : '') . '"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z"/></svg>';
    }
    $html .= '</div>';
    return $html;
};
?>

<?php if (empty($m['kelompok_id'])): ?>
    <!-- No Group Empty State - shown FIRST, no hero/feedback shown -->
    <div class="flex flex-col items-center justify-center py-24 px-6 text-center">
        <div class="mx-auto mb-6 grid h-24 w-24 place-items-center rounded-3xl bg-amber-50 text-amber-400 shadow-sm ring-1 ring-amber-100 dark:bg-amber-900/20 dark:ring-amber-900/50">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" class="h-12 w-12">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/>
            </svg>
        </div>
        <h3 class="text-xl font-extrabold text-slate-800 dark:text-white">Belum Ada Kelompok KKN</h3>
        <p class="mt-3 max-w-md text-sm text-slate-400 leading-relaxed">Anda belum ditempatkan di kelompok KKN. Hubungi admin kampus untuk mendapatkan penempatan sebelum Anda dapat mengisi evaluasi.</p>
        <div class="mt-8 flex flex-col sm:flex-row items-center gap-3">
            <div class="flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-left dark:border-amber-900/50 dark:bg-amber-900/20">
                <div class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-amber-100 text-amber-600 dark:bg-amber-900/50">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-amber-700 dark:text-amber-300">Hubungi admin kampus untuk penempatan kelompok KKN</span>
            </div>
        </div>
    </div>
<?php else: ?>

<!-- Beautiful Hero Strip -->
<div class="hero-strip mb-8 relative overflow-hidden rounded-3xl bg-gradient-to-br from-violet-700 via-indigo-700 to-blue-800 p-8 text-white shadow-xl">
    <!-- Background decorations -->
    <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
    <div class="absolute -bottom-24 -left-24 h-80 w-80 rounded-full bg-indigo-500/20 blur-3xl"></div>
    
    <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <div class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-extrabold tracking-widest text-violet-100 backdrop-blur-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                UMPAN BALIK KKN TEMATIK
            </div>
            <h2 class="mt-4 text-3xl sm:text-4xl font-extrabold tracking-tight text-white drop-shadow-sm">Evaluasi Kegiatan</h2>
            <p class="mt-3 max-w-xl text-sm sm:text-base leading-relaxed text-indigo-100">
                Berikan penilaian jujur terhadap pelaksanaan kegiatan KKN yang telah Anda ikuti. Masukan Anda sangat berharga dan digunakan secara rahasia oleh admin &amp; DPL untuk perbaikan program di masa depan.
            </p>
        </div>
        
        <?php if ($e): ?>
        <!-- Big overall rating badge on hero -->
        <div class="flex flex-col items-center justify-center rounded-2xl bg-white/10 p-5 ring-1 ring-white/20 backdrop-blur-md">
            <span class="text-xs font-extrabold uppercase tracking-wider text-violet-200">RATING ANDA</span>
            <div class="mt-1 flex items-end gap-1">
                <strong class="text-5xl font-extrabold text-white drop-shadow-md"><?= (int) $e['rating'] ?></strong>
                <span class="mb-1 text-lg font-bold text-violet-300">/5</span>
            </div>
            <div class="mt-2">
                <?= $stars((int) $e['rating'], 'h-4 w-4 drop-shadow-sm') ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if (! empty($evaluasiAdmin)): ?>
<section class="mb-6 overflow-hidden rounded-2xl border border-violet-200 bg-white shadow-sm dark:border-violet-900/50 dark:bg-slate-900">
    <div class="flex flex-col gap-4 border-b border-violet-100 bg-violet-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-violet-900/50 dark:bg-violet-950/35">
        <div>
            <span class="text-xs font-extrabold uppercase tracking-wider text-violet-600 dark:text-violet-300">Umpan balik admin</span>
            <h3 class="mt-1 text-lg font-extrabold text-slate-900 dark:text-white">Evaluasi untuk perkembangan Anda</h3>
        </div>
        <div class="flex items-center gap-2 rounded-xl bg-white px-3 py-2 shadow-sm dark:bg-slate-800">
            <?= $stars((int) ($evaluasiAdmin['rating'] ?? 0), 'h-5 w-5') ?>
            <strong class="text-sm text-slate-900 dark:text-white"><?= (int) ($evaluasiAdmin['rating'] ?? 0) ?>/5</strong>
        </div>
    </div>
    <div class="grid gap-4 p-5 sm:grid-cols-[auto_1fr]">
        <div><span class="inline-flex rounded-full bg-violet-100 px-3 py-1.5 text-sm font-extrabold text-violet-700 dark:bg-violet-950/60 dark:text-violet-300"><?= esc($evaluasiAdmin['kategori'] ?? '-') ?></span></div>
        <div>
            <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Catatan / arahan revisi</h4>
            <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700 dark:text-slate-200"><?= esc($evaluasiAdmin['komentar'] ?? $evaluasiAdmin['rekomendasi'] ?? '-') ?></p>
        </div>
    </div>
    <?php if ($adminCriteria !== []): ?>
        <div class="border-t border-violet-100 px-5 py-5 dark:border-violet-900/50">
            <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Rincian kriteria</h4>
            <div class="mt-3 grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($adminCriteria as $criterion): ?>
                    <?php $criterionRating = (int) ($criterion['rating'] ?? 0); ?>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-800 dark:bg-slate-800/60">
                        <div class="flex items-start justify-between gap-3">
                            <span class="text-sm font-bold text-slate-700 dark:text-slate-200"><?= esc($criterion['nama'] ?? '-') ?></span>
                            <span class="shrink-0 font-mono text-xs font-extrabold text-amber-600 dark:text-amber-300"><?= $criterionRating ?>/5</span>
                        </div>
                        <div class="mt-2"><?= $stars($criterionRating, 'h-4 w-4') ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</section>
<?php endif; ?>

<?php if ($e && !isset($_GET['edit'])): ?>
<!-- Read-Only View of Submitted Evaluation -->
<div class="card overflow-hidden">
    <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/50 px-6 py-4 dark:border-slate-800 dark:bg-slate-900/50">
        <div class="flex items-center gap-3">
            <span class="stempel stempel-divalidasi">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3 w-3"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                Terkirim & Tersimpan
            </span>
            <span class="text-xs text-slate-400">Tanggal: <?= format_tanggal($e['created_at']) ?></span>
        </div>
        <a href="?edit=1" class="btn btn-secondary btn-sm bg-white dark:bg-slate-800">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="mr-1 h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
            Perbarui Evaluasi
        </a>
    </div>

    <div class="p-6 sm:p-8">
        <div class="grid gap-8 md:grid-cols-2">
            <!-- Left side: Detailed Aspek -->
            <div class="space-y-6">
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Kategori Evaluasi</h4>
                    <span class="mt-1 inline-flex items-center rounded-lg bg-blue-50 px-3 py-1.5 text-sm font-extrabold text-blue-700 ring-1 ring-inset ring-blue-700/10 dark:bg-blue-900/30 dark:text-blue-400 dark:ring-blue-900">
                        <?= esc($e['kategori'] ?? 'Umum') ?>
                    </span>
                </div>

                <div class="space-y-4">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2 dark:border-slate-800">Penilaian Aspek Terperinci</h4>
                    
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 p-4 dark:bg-slate-800/50">
                        <div>
                            <strong class="block text-sm text-slate-900 dark:text-white">Bimbingan DPL</strong>
                            <small class="text-xs text-slate-500">Kualitas & keaktifan pendampingan</small>
                        </div>
                        <div class="flex flex-col items-end">
                            <?= $stars((int) ($e['aspek_bimbingan'] ?? 0), 'h-4 w-4') ?>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 p-4 dark:bg-slate-800/50">
                        <div>
                            <strong class="block text-sm text-slate-900 dark:text-white">Lokasi & Fasilitas</strong>
                            <small class="text-xs text-slate-500">Kondisi tempat KKN</small>
                        </div>
                        <div class="flex flex-col items-end">
                            <?= $stars((int) ($e['aspek_lokasi'] ?? 0), 'h-4 w-4') ?>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 p-4 dark:bg-slate-800/50">
                        <div>
                            <strong class="block text-sm text-slate-900 dark:text-white">Pelaksanaan Program</strong>
                            <small class="text-xs text-slate-500">Kelancaran kegiatan tematik</small>
                        </div>
                        <div class="flex flex-col items-end">
                            <?= $stars((int) ($e['aspek_pelaksanaan'] ?? 0), 'h-4 w-4') ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right side: Commentary -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Komentar & Saran Konstruktif</h4>
                <div class="relative h-full min-h-[200px] rounded-2xl bg-amber-50 p-6 shadow-inner dark:bg-amber-900/10">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="absolute left-4 top-4 h-8 w-8 text-amber-200 dark:text-amber-900/30 opacity-50"><path d="M14 17h3l2-4V7h-6v6h3M6 17h3l2-4V7H5v6h3"/></svg>
                    <p class="relative z-10 text-slate-700 dark:text-slate-300 leading-relaxed italic">
                        <?= nl2br(esc($e['komentar'] ?? 'Tidak ada komentar yang disertakan.')) ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php else: ?>

<!-- Form Mode: Create or Update -->
<div class="card border-violet-100 shadow-xl shadow-violet-900/5 dark:border-violet-900/30">
    <div class="card-head border-b border-slate-100 bg-white px-6 py-5 dark:border-slate-800 dark:bg-slate-900 -mx-5 -mt-5 mb-5 rounded-t-2xl">
        <h2 class="text-xl flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-6 w-6 text-violet-500"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            <?= $e ? 'Perbarui Evaluasi Anda' : 'Formulir Evaluasi Kegiatan' ?>
        </h2>
        <?php if ($e): ?>
            <a href="?" class="text-xs font-bold text-slate-500 hover:text-slate-800 dark:hover:text-white">Batal Perbarui</a>
        <?php endif; ?>
    </div>

    <?php if (! empty(session('errors'))): ?>
        <div class="alert alert-danger mx-5">
            <strong class="block mb-1">Terjadi kesalahan:</strong>
            <ul class="list-inside list-disc pl-2">
            <?php foreach ((array) session('errors') as $err): ?>
                <li><?= esc(is_array($err) ? implode(', ', $err) : $err) ?></li>
            <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= site_url('mahasiswa/evaluasi') ?>" class="p-1 sm:px-4">
        <?= csrf_field() ?>

        <!-- Primary Overall Rating - Giant Stars -->
        <div class="field mb-8 text-center rounded-2xl bg-slate-50 py-8 px-4 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
            <label class="block text-lg font-extrabold text-slate-900 dark:text-white mb-2">Rating Keseluruhan KKN</label>
            <p class="text-sm text-slate-500 mb-6">Seberapa puas Anda dengan pelaksanaan KKN Tematik ini secara keseluruhan?</p>
            
            <div class="star-rating justify-center gap-2" id="star-rating">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <label class="star-label cursor-pointer p-2 rounded-xl transition hover:bg-white dark:hover:bg-slate-700 hover:shadow-sm" data-value="<?= $i ?>">
                        <input type="radio" name="rating" value="<?= $i ?>" <?= (string) old('rating', $e['rating'] ?? '') === (string) $i ? 'checked' : '' ?> required class="sr-only">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="star-icon h-10 w-10 sm:h-12 sm:w-12 drop-shadow-sm">
                            <path d="m12 2 3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z"/>
                        </svg>
                    </label>
                <?php endfor; ?>
            </div>
            <div class="mt-4 h-6">
                <span class="star-rating-text inline-flex rounded-full px-4 py-1 text-sm font-extrabold transition-colors" id="star-rating-text"></span>
            </div>
        </div>

        <div class="form-grid">
            <div class="field col-span-full mb-6 border-b border-slate-100 pb-6 dark:border-slate-800">
                <label class="text-base">Kategori Utama Evaluasi</label>
                <div class="mt-2">
                    <select id="kategori_select" class="form-input rounded-xl border-2 border-slate-200 focus:border-violet-500 p-3 shadow-sm bg-white text-slate-900 dark:bg-slate-800 dark:border-slate-700 dark:text-white font-semibold w-full sm:w-1/2">
                        <option value="">— Pilih fokus evaluasi Anda —</option>
                        <option value="Kegiatan Lapangan">Kegiatan Lapangan Program Kerja</option>
                        <option value="Bimbingan DPL">Bimbingan DPL & Arahan</option>
                        <option value="Administrasi">Administrasi & Birokrasi Kampus</option>
                        <option value="Logistik">Logistik & Fasilitas Lokasi</option>
                        <option value="Umum">Umum / Lainnya</option>
                        <option value="__custom__">Tulis Sendiri (Custom)...</option>
                    </select>
                    <input type="text" id="kategori_custom" class="form-input hidden mt-3 w-full rounded-xl border-2 border-violet-200 bg-violet-50 text-slate-900 p-3 shadow-sm focus:border-violet-500 dark:border-violet-900 dark:bg-violet-900/40 dark:text-white" placeholder="Ketik topik fokus evaluasi Anda..." maxlength="100">
                    <input type="hidden" name="kategori" id="kategori_hidden" value="<?= esc(old('kategori', $e['kategori'] ?? '')) ?>" required>
                </div>
            </div>

            <!-- Detail Ratings using Star Widgets -->
            <div class="field mb-6 rounded-xl border border-slate-100 p-4 dark:border-slate-800">
                <label class="text-sm font-extrabold text-slate-800 dark:text-white">Kualitas Bimbingan DPL</label>
                <span class="block text-xs text-slate-500 mb-3">Keaktifan pendampingan, responsibilitas, dan kualitas arahan DPL.</span>
                
                <div class="star-rating-mini" data-name="aspek_bimbingan">
                    <input type="hidden" name="aspek_bimbingan" value="<?= esc(old('aspek_bimbingan', $e['aspek_bimbingan'] ?? '')) ?>" required>
                    <div class="flex gap-1">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <button type="button" class="mini-star-btn p-1 text-slate-200 hover:text-amber-300 transition-transform hover:scale-110 focus:outline-none" data-val="<?= $i ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-8 w-8 fill-current"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z"/></svg>
                            </button>
                        <?php endfor; ?>
                    </div>
                    <span class="mini-star-text text-xs font-bold mt-1 block h-4 text-amber-600"></span>
                </div>
            </div>

            <div class="field mb-6 rounded-xl border border-slate-100 p-4 dark:border-slate-800">
                <label class="text-sm font-extrabold text-slate-800 dark:text-white">Lokasi & Fasilitas Lapangan</label>
                <span class="block text-xs text-slate-500 mb-3">Kesesuaian desa penempatan, keamanan, dan logistik posko.</span>
                
                <div class="star-rating-mini" data-name="aspek_lokasi">
                    <input type="hidden" name="aspek_lokasi" value="<?= esc(old('aspek_lokasi', $e['aspek_lokasi'] ?? '')) ?>" required>
                    <div class="flex gap-1">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <button type="button" class="mini-star-btn p-1 text-slate-200 hover:text-amber-300 transition-transform hover:scale-110 focus:outline-none" data-val="<?= $i ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-8 w-8 fill-current"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z"/></svg>
                            </button>
                        <?php endfor; ?>
                    </div>
                    <span class="mini-star-text text-xs font-bold mt-1 block h-4 text-amber-600"></span>
                </div>
            </div>

            <div class="field col-span-full mb-6 rounded-xl border border-slate-100 p-4 dark:border-slate-800 md:w-1/2">
                <label class="text-sm font-extrabold text-slate-800 dark:text-white">Pelaksanaan Kegiatan & Proker</label>
                <span class="block text-xs text-slate-500 mb-3">Kelancaran kegiatan, partisipasi masyarakat, dan kebermanfaatan.</span>
                
                <div class="star-rating-mini" data-name="aspek_pelaksanaan">
                    <input type="hidden" name="aspek_pelaksanaan" value="<?= esc(old('aspek_pelaksanaan', $e['aspek_pelaksanaan'] ?? '')) ?>" required>
                    <div class="flex gap-1">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <button type="button" class="mini-star-btn p-1 text-slate-200 hover:text-amber-300 transition-transform hover:scale-110 focus:outline-none" data-val="<?= $i ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-8 w-8 fill-current"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z"/></svg>
                            </button>
                        <?php endfor; ?>
                    </div>
                    <span class="mini-star-text text-xs font-bold mt-1 block h-4 text-amber-600"></span>
                </div>
            </div>
        </div>

        <div class="field mt-2 mb-8">
            <label class="text-base font-extrabold">Komentar, Kesan, atau Saran Konstruktif</label>
            <span class="block text-xs text-slate-500 mb-2">Tuliskan pengalaman spesifik Anda atau saran perbaikan untuk KKN periode selanjutnya.</span>
            <textarea name="komentar" rows="5" class="w-full p-4 bg-slate-50 text-slate-900 border-2 border-slate-200 focus:border-violet-500 focus:bg-white dark:bg-slate-800/50 dark:border-slate-700 dark:text-white dark:focus:bg-slate-800 transition-colors text-sm rounded-xl shadow-sm" placeholder="Ceritakan pengalaman Anda, apa yang berjalan baik, dan apa yang bisa diperbaiki…"><?= esc(old('komentar', $e['komentar'] ?? '')) ?></textarea>
        </div>

        <div class="form-actions mt-8 border-t border-slate-100 pt-6 dark:border-slate-800">
            <button type="submit" class="btn btn-primary px-8 text-base">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="mr-2 h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                <?= $e ? 'Simpan Perubahan' : 'Kirim Evaluasi Final' ?>
            </button>
            <a href="<?= site_url('mahasiswa/dashboard') ?>" class="btn btn-secondary px-6">Kembali ke Dashboard</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Labels configuration
    const ratingLabels = ['', 'Sangat Kurang', 'Kurang', 'Cukup', 'Baik', 'Sangat Baik'];
    const ratingColors = ['', 'bg-rose-100 text-rose-700', 'bg-orange-100 text-orange-700', 'bg-amber-100 text-amber-700', 'bg-blue-100 text-blue-700', 'bg-emerald-100 text-emerald-700'];

    /* Giant Star Rating (Overall) */
    const starRating = document.getElementById('star-rating');
    const starText = document.getElementById('star-rating-text');

    if (starRating) {
        const labels = starRating.querySelectorAll('.star-label');
        let currentRating = 0;

        const checked = starRating.querySelector('input[name="rating"]:checked');
        if (checked) {
            currentRating = parseInt(checked.value);
            updateStars(currentRating);
        }

        labels.forEach(function(label) {
            label.addEventListener('mouseenter', function() {
                highlightStars(parseInt(this.dataset.value));
            });

            label.addEventListener('mouseleave', function() {
                highlightStars(currentRating);
            });

            label.addEventListener('click', function() {
                currentRating = parseInt(this.dataset.value);
                updateStars(currentRating);
            });
        });

        function highlightStars(value) {
            labels.forEach(function(l) {
                const v = parseInt(l.dataset.value);
                const icon = l.querySelector('.star-icon');
                if(icon) {
                    icon.classList.toggle('filled', v <= value);
                    icon.classList.toggle('hover', v === value);
                }
            });
            if (starText) {
                starText.textContent = ratingLabels[value] || '';
                starText.className = 'star-rating-text inline-flex rounded-full px-4 py-1 text-sm font-extrabold transition-colors ' + (ratingColors[value] || '');
            }
        }

        function updateStars(value) {
            highlightStars(value);
        }
    }
    
    /* Mini Star Ratings (for Aspek) */
    const miniRatings = document.querySelectorAll('.star-rating-mini');
    miniRatings.forEach(function(container) {
        const hiddenInput = container.querySelector('input[type="hidden"]');
        const buttons = container.querySelectorAll('.mini-star-btn');
        const textDisplay = container.querySelector('.mini-star-text');
        
        let currentVal = parseInt(hiddenInput.value) || 0;
        
        // Init visual state
        function renderMiniStars(val) {
            buttons.forEach(btn => {
                const v = parseInt(btn.dataset.val);
                if (v <= val) {
                    btn.classList.add('text-amber-400');
                    btn.classList.remove('text-slate-200');
                } else {
                    btn.classList.remove('text-amber-400');
                    btn.classList.add('text-slate-200');
                }
            });
            if (textDisplay) textDisplay.textContent = val > 0 ? ratingLabels[val] : '';
        }
        
        if (currentVal > 0) renderMiniStars(currentVal);
        
        buttons.forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                renderMiniStars(parseInt(this.dataset.val));
            });
            btn.addEventListener('mouseleave', function() {
                renderMiniStars(currentVal);
            });
            btn.addEventListener('click', function() {
                currentVal = parseInt(this.dataset.val);
                hiddenInput.value = currentVal;
                renderMiniStars(currentVal);
            });
        });
    });

    /* Custom Tipe Evaluasi */
    const selectEl = document.getElementById('kategori_select');
    const customInput = document.getElementById('kategori_custom');
    const hiddenInput = document.getElementById('kategori_hidden');

    if (selectEl && customInput && hiddenInput) {
        const oldVal = hiddenInput.value;
        if (oldVal) {
            const optionExists = Array.from(selectEl.options).some(function(opt) {
                return opt.value === oldVal && opt.value !== '__custom__';
            });
            if (optionExists) {
                selectEl.value = oldVal;
            } else {
                selectEl.value = '__custom__';
                customInput.classList.remove('hidden');
                customInput.value = oldVal;
                customInput.required = true;
            }
        }

        selectEl.addEventListener('change', function() {
            if (this.value === '__custom__') {
                customInput.classList.remove('hidden');
                customInput.required = true;
                customInput.focus();
                hiddenInput.value = customInput.value;
            } else {
                customInput.classList.add('hidden');
                customInput.required = false;
                customInput.value = '';
                hiddenInput.value = this.value;
            }
        });

        customInput.addEventListener('input', function() {
            hiddenInput.value = this.value;
        });

        if (selectEl.value && selectEl.value !== '__custom__') {
            hiddenInput.value = selectEl.value;
        }
    }
});
</script>
<?php endif; ?>
<?php endif; ?>
