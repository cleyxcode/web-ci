<div class="card" style="max-width:560px">
    <div class="card-head"><h2>Nilai KKN</h2></div>
    <?php if (empty($nilai)): ?>
        <p class="empty">Nilai belum dipublikasikan DPL.</p>
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
            <div class="stat">
                <div class="label">Prediksi KNN</div>
                <div class="value font-mono"><?= esc($nilai['prediksi_knn'] ?? '-') ?></div>
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
            <p style="margin-top:16px;font-size:0.9rem;color:var(--abu-karang)"><strong>Catatan DPL:</strong> <?= esc($nilai['catatan']) ?></p>
        <?php endif; ?>
    <?php endif; ?>
</div>
