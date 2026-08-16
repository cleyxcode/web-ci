<div class="card">
    <div class="card-head"><h2>Nilai KKN</h2></div>
    <?php if (empty($nilai)): ?>
        <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
            <div class="mx-auto mb-6 grid h-20 w-20 place-items-center rounded-2xl bg-amber-50 text-amber-400 shadow-sm ring-1 ring-amber-100 dark:bg-amber-900/20 dark:ring-amber-900/50">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" class="h-10 w-10">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m12 2 3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z"/>
                </svg>
            </div>
            <h3 class="text-base font-extrabold text-slate-800 dark:text-white">Nilai Belum Tersedia</h3>
            <p class="mt-2 max-w-sm text-sm text-slate-400 leading-relaxed">Nilai Anda belum dipublikasikan oleh DPL. Setelah DPL melakukan penilaian, hasilnya akan tampil di halaman ini.</p>
            <div class="mt-6 flex items-center gap-2 rounded-xl bg-slate-50 px-4 py-3 text-xs text-slate-400 dark:bg-slate-800">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-4 w-4 shrink-0 text-blue-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                </svg>
                Pastikan seluruh logbook & laporan Anda telah terverifikasi
            </div>
        </div>
    <?php else: ?>
        <div class="stat-row">
            <div class="stat">
                <div class="label">Nilai akhir</div>
                <div class="value"><?= esc(number_format((float) $nilai['nilai_akhir'], 2)) ?></div>
            </div>
            <div class="stat">
                <div class="label">Grade</div>
                <div class="value <?= grade_class($nilai['grade'] ?? null) ?>"><?= esc($nilai['grade'] ?? '-') ?></div>
            </div>
        </div>
        <div class="table-wrap">
            <table class="data">
                <tr><th>Komponen</th><th>Bobot</th><th>Nilai</th></tr>
                <tr><td>Keaktifan</td><td>30%</td><td class="font-mono"><?= esc($nilai['nilai_keaktifan']) ?></td></tr>
                <tr><td>Logbook</td><td>30%</td><td class="font-mono"><?= esc($nilai['nilai_logbook']) ?></td></tr>
                <tr><td>Laporan</td><td>40%</td><td class="font-mono"><?= esc($nilai['nilai_laporan']) ?></td></tr>
            </table>
        </div>
        <?php if (! empty($nilai['catatan'])): ?>
            <p><strong>Catatan DPL:</strong> <?= esc($nilai['catatan']) ?></p>
        <?php endif; ?>
    <?php endif; ?>
</div>
