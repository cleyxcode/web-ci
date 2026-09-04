<div class="card">
    <div class="card-head">
        <h2>Laporan kegiatan</h2>
        <a href="<?= site_url('mahasiswa/laporan/create') ?>" class="btn btn-primary btn-sm">+ Upload</a>
    </div>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Deskripsi</th>
                    <th>File</th>
                    <th>Status</th>
                    <th>Catatan</th>
                    <th style="width:1%">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($laporan)): ?>
                <tr><td colspan="6" class="empty">Belum ada laporan.</td></tr>
            <?php else: ?>
                <?php foreach ($laporan as $row): ?>
                    <tr>
                        <td><?= esc($row['judul']) ?></td>
                        <td><?= esc(mb_strimwidth($row['deskripsi'] ?? '', 0, 60, '…')) ?></td>
                        <td>
                            <?php if (! empty($row['file_laporan'])): ?>
                                <a href="<?= base_url('uploads/' . $row['file_laporan']) ?>" target="_blank">PDF</a>
                            <?php else: ?>-<?php endif; ?>
                        </td>
                        <td><span class="<?= stempel_class($row['status']) ?>"><?= stempel_label($row['status']) ?></span></td>
                        <td><?= esc($row['catatan_dpl'] ?? '-') ?></td>
                        <td style="white-space: nowrap">
                            <?php if ($row['status'] !== 'diterima'): ?>
                                <form method="post" action="<?= site_url('mahasiswa/laporan/' . $row['id'] . '/delete') ?>" onsubmit="return confirm('Hapus laporan ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
