<div class="card">
    <div class="card-head">
        <h2>Review laporan</h2>
        <a href="<?= site_url('dpl/penilaian') ?>" class="btn btn-secondary btn-sm">Buka penilaian</a>
    </div>
    <form method="get" class="filter-bar">
        <div class="field">
            <label for="laporan-status">Tampilkan status</label>
            <select id="laporan-status" name="status">
                <option value="">Semua status</option>
                <option value="menunggu" <?= ($filterStatus ?? '') === 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                <option value="diterima" <?= ($filterStatus ?? '') === 'diterima' ? 'selected' : '' ?>>Diterima</option>
                <option value="ditolak" <?= ($filterStatus ?? '') === 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Terapkan</button>
        <?php if (! empty($filterStatus)): ?><a href="<?= site_url('dpl/laporan') ?>" class="btn btn-secondary btn-sm">Reset</a><?php endif; ?>
    </form>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Mahasiswa</th>
                    <th>Kelompok</th>
                    <th>File</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($laporan)): ?>
                <tr><td colspan="6" class="empty">Tidak ada laporan.</td></tr>
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
                            <?= esc($row['nama_kelompok'] ?? '-') ?><br>
                            <span class="field-hint"><?= esc(format_alamat($row)) ?></span>
                        </td>
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
