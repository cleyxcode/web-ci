<?php
$stars = static function (?int $value): string {
    if ($value === null || $value < 1) {
        return '-';
    }
    $html = '';
    for ($i = 1; $i <= 5; $i++) {
        $html .= $i <= $value ? '★' : '☆';
    }

    return $html;
};
?>

<div class="stat-row">
    <div class="stat">
        <div class="label">Evaluasi masuk</div>
        <div class="value"><?= (int) ($totalEvaluasi ?? 0) ?> / <?= (int) ($totalMahasiswa ?? 0) ?></div>
        <small>mahasiswa bimbingan</small>
    </div>
    <div class="stat">
        <div class="label">Rata-rata rating</div>
        <div class="value"><?= $avgRating !== null ? esc((string) $avgRating) : '-' ?></div>
        <small>skala 1–5</small>
    </div>
    <div class="stat">
        <div class="label">Belum mengisi</div>
        <div class="value"><?= count($belumEvaluasi ?? []) ?></div>
    </div>
</div>

<div class="card" style="margin-bottom:16px">
    <div class="card-head"><h2>Evaluasi kegiatan mahasiswa bimbingan</h2></div>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>Mahasiswa</th>
                    <th>Kelompok</th>
                    <th>Rating</th>
                    <th>Bimbingan</th>
                    <th>Lokasi</th>
                    <th>Pelaksanaan</th>
                    <th>Kategori</th>
                    <th>Komentar</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($evaluasi)): ?>
                <tr><td colspan="9" class="empty">Belum ada evaluasi dari mahasiswa bimbingan. Mereka mengisi lewat menu Evaluasi Kegiatan.</td></tr>
            <?php else: ?>
                <?php foreach ($evaluasi as $row): ?>
                    <tr>
                        <td>
                            <strong><?= esc($row['nama_mahasiswa'] ?? '-') ?></strong><br>
                            <span class="font-mono" style="font-size:0.8rem"><?= esc($row['npm'] ?? '') ?></span>
                        </td>
                        <td><?= esc($row['nama_kelompok'] ?? '-') ?></td>
                        <td style="color:var(--kuning-senja);letter-spacing:1px"><?= $stars((int) ($row['rating'] ?? 0)) ?></td>
                        <td class="font-mono"><?= (int) ($row['aspek_bimbingan'] ?? 0) ?></td>
                        <td class="font-mono"><?= (int) ($row['aspek_lokasi'] ?? 0) ?></td>
                        <td class="font-mono"><?= (int) ($row['aspek_pelaksanaan'] ?? 0) ?></td>
                        <td><?= esc($row['kategori'] ?? '-') ?></td>
                        <td><?= esc(mb_strimwidth($row['komentar'] ?? '-', 0, 80, '…')) ?></td>
                        <td><?= format_tanggal($row['created_at'] ?? null) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (! empty($belumEvaluasi)): ?>
<div class="card">
    <div class="card-head"><h2>Belum mengirim evaluasi</h2></div>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr><th>NPM</th><th>Nama</th><th>Prodi</th></tr>
            </thead>
            <tbody>
                <?php foreach ($belumEvaluasi as $row): ?>
                    <tr>
                        <td class="font-mono"><?= esc($row['npm']) ?></td>
                        <td><?= esc($row['nama']) ?></td>
                        <td><?= esc($row['prodi'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
