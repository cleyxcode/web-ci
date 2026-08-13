<div class="card">
    <div class="card-head"><h2>Review laporan</h2></div>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Mahasiswa</th>
                    <th>File</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($laporan)): ?>
                <tr><td colspan="5" class="empty">Tidak ada laporan.</td></tr>
            <?php else: ?>
                <?php foreach ($laporan as $row): ?>
                    <tr>
                        <td>
                            <strong><?= esc($row['judul']) ?></strong>
                            <?php if (! empty($row['deskripsi'])): ?>
                                <div class="field-hint"><?= esc(mb_strimwidth($row['deskripsi'], 0, 80, '…')) ?></div>
                            <?php endif; ?>
                        </td>
                        <td><?= esc($row['nama_mahasiswa'] ?? $row['nama'] ?? '-') ?></td>
                        <td>
                            <?php if (! empty($row['file_laporan'])): ?>
                                <a href="<?= base_url('uploads/' . $row['file_laporan']) ?>" target="_blank">PDF</a>
                            <?php else: ?>-<?php endif; ?>
                        </td>
                        <td><span class="<?= stempel_class($row['status']) ?>"><?= stempel_label($row['status']) ?></span></td>
                        <td>
                            <?php if ($row['status'] === 'menunggu'): ?>
                                <form method="post" action="<?= site_url('dpl/laporan/' . $row['id'] . '/review') ?>" style="display:flex;flex-direction:column;gap:6px;min-width:180px">
                                    <?= csrf_field() ?>
                                    <input type="text" name="catatan_dpl" placeholder="Catatan (opsional)" style="padding:6px 8px;background:var(--inset);border:1px solid var(--border-lembut);border-radius:6px;font-size:0.8rem">
                                    <div class="actions">
                                        <button type="submit" name="action" value="terima" class="btn btn-success btn-sm">Terima</button>
                                        <button type="submit" name="action" value="tolak" class="btn btn-danger btn-sm">Tolak</button>
                                    </div>
                                </form>
                            <?php else: ?>
                                <span style="color:var(--tinta-redup);font-size:0.8rem"><?= esc($row['catatan_dpl'] ?? 'Selesai') ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
