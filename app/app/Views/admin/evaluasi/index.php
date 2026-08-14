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
        <div class="label">Total evaluasi</div>
        <div class="value"><?= (int) ($totalEvaluasi ?? 0) ?></div>
        <small>dari <?= (int) ($totalMahasiswa ?? 0) ?> mahasiswa</small>
    </div>
    <div class="stat">
        <div class="label">Rata-rata rating</div>
        <div class="value"><?= $avgRating !== null ? esc((string) $avgRating) : '-' ?></div>
        <small>skala 1–5</small>
    </div>
    <div class="stat">
        <div class="label">Partisipasi</div>
        <div class="value">
            <?php
            $pct = ($totalMahasiswa ?? 0) > 0
                ? round(((int) ($totalEvaluasi ?? 0) / (int) $totalMahasiswa) * 100)
                : 0;
            echo (int) $pct;
            ?>%
        </div>
    </div>
</div>

<div class="card">
    <div class="card-head">
        <h2>Semua evaluasi kegiatan KKN</h2>
        <a href="<?= site_url('admin/pengumuman/create') ?>" class="btn btn-secondary btn-sm">Buat pengumuman</a>
    </div>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>Mahasiswa</th>
                    <th>Kelompok</th>
                    <th>DPL</th>
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
                <tr><td colspan="10" class="empty">Belum ada evaluasi. Mahasiswa mengirim lewat menu Evaluasi Kegiatan.</td></tr>
            <?php else: ?>
                <?php foreach ($evaluasi as $row): ?>
                    <tr>
                        <td>
                            <strong><?= esc($row['nama_mahasiswa'] ?? '-') ?></strong><br>
                            <span class="font-mono" style="font-size:0.8rem"><?= esc($row['npm'] ?? '') ?></span>
                        </td>
                        <td><?= esc($row['nama_kelompok'] ?? '-') ?></td>
                        <td><?= esc($row['nama_dpl'] ?? '-') ?></td>
                        <td style="color:var(--kuning-senja);letter-spacing:1px"><?= $stars((int) ($row['rating'] ?? 0)) ?></td>
                        <td class="font-mono"><?= (int) ($row['aspek_bimbingan'] ?? 0) ?></td>
                        <td class="font-mono"><?= (int) ($row['aspek_lokasi'] ?? 0) ?></td>
                        <td class="font-mono"><?= (int) ($row['aspek_pelaksanaan'] ?? 0) ?></td>
                        <td><?= esc($row['kategori'] ?? '-') ?></td>
                        <td><?= esc(mb_strimwidth($row['komentar'] ?? '-', 0, 70, '…')) ?></td>
                        <td><?= format_tanggal($row['created_at'] ?? null) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
