<div class="card">
    <div class="card-head"><h2>Semua laporan</h2></div>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Mahasiswa</th>
                    <th>NPM</th>
                    <th>Status</th>
                    <th>File</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($laporan)): ?>
                <tr><td colspan="6" class="empty">Belum ada laporan.</td></tr>
            <?php else: ?>
                <?php foreach ($laporan as $row): ?>
                    <tr>
                        <td><?= esc($row['judul']) ?></td>
                        <td><?= esc($row['nama_mahasiswa'] ?? $row['nama'] ?? '-') ?></td>
                        <td class="font-mono"><?= esc($row['npm'] ?? '-') ?></td>
                        <td><span class="<?= stempel_class($row['status']) ?>"><?= stempel_label($row['status']) ?></span></td>
                        <td>
                            <?php if (! empty($row['file_laporan'])): ?>
                                <a href="<?= base_url('uploads/' . $row['file_laporan']) ?>" target="_blank">PDF</a>
                            <?php else: ?>-<?php endif; ?>
                        </td>
                        <td><?= format_tanggal($row['created_at'] ?? null) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
