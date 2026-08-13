<div class="card">
    <div class="card-head"><h2>Antrian validasi logbook</h2></div>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Mahasiswa</th>
                    <th>Kegiatan</th>
                    <th>Dok</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($logbooks)): ?>
                <tr><td colspan="6" class="empty">Tidak ada logbook.</td></tr>
            <?php else: ?>
                <?php foreach ($logbooks as $row): ?>
                    <tr>
                        <td><?= format_tanggal($row['tanggal'] ?? null) ?></td>
                        <td>
                            <strong><?= esc($row['nama_mahasiswa'] ?? $row['nama'] ?? '-') ?></strong><br>
                            <span class="font-mono" style="font-size:0.75rem;color:var(--tinta-redup)"><?= esc($row['npm'] ?? '') ?></span>
                        </td>
                        <td>
                            <?= esc($row['kegiatan']) ?>
                            <?php if (! empty($row['catatan_dpl'])): ?>
                                <div class="field-hint">Catatan: <?= esc($row['catatan_dpl']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (! empty($row['dokumentasi'])): ?>
                                <a href="<?= base_url('uploads/' . $row['dokumentasi']) ?>" target="_blank">Lihat</a>
                            <?php else: ?>-<?php endif; ?>
                        </td>
                        <td><span class="<?= stempel_class($row['status']) ?>"><?= stempel_label($row['status']) ?></span></td>
                        <td>
                            <?php if ($row['status'] === 'menunggu'): ?>
                                <form method="post" action="<?= site_url('dpl/logbook/' . $row['id'] . '/proses') ?>" style="display:flex;flex-direction:column;gap:6px;min-width:180px">
                                    <?= csrf_field() ?>
                                    <input type="text" name="catatan_dpl" placeholder="Catatan (opsional)" style="padding:6px 8px;background:var(--inset);border:1px solid var(--border-lembut);border-radius:6px;font-size:0.8rem">
                                    <div class="actions">
                                        <button type="submit" name="action" value="validasi" class="btn btn-success btn-sm">Validasi</button>
                                        <button type="submit" name="action" value="tolak" class="btn btn-danger btn-sm">Tolak</button>
                                    </div>
                                </form>
                            <?php else: ?>
                                <span style="color:var(--tinta-redup);font-size:0.8rem">Selesai</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
