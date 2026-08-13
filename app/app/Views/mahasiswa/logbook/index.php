<div class="card">
    <div class="card-head">
        <h2>Logbook kegiatan</h2>
        <a href="<?= site_url('mahasiswa/logbook/create') ?>" class="btn btn-primary btn-sm">+ Tambah</a>
    </div>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Kegiatan</th>
                    <th>Lokasi</th>
                    <th>Dok</th>
                    <th>Status</th>
                    <th>Catatan DPL</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($logbooks)): ?>
                <tr><td colspan="6" class="empty">Belum ada logbook.</td></tr>
            <?php else: ?>
                <?php foreach ($logbooks as $row): ?>
                    <tr>
                        <td><?= format_tanggal($row['tanggal'] ?? null) ?></td>
                        <td><?= esc($row['kegiatan']) ?></td>
                        <td><?= esc($row['lokasi_kegiatan'] ?? '-') ?></td>
                        <td>
                            <?php if (! empty($row['dokumentasi'])): ?>
                                <a href="<?= base_url('uploads/' . $row['dokumentasi']) ?>" target="_blank">Lihat</a>
                            <?php else: ?>-<?php endif; ?>
                        </td>
                        <td><span class="<?= stempel_class($row['status']) ?>"><?= stempel_label($row['status']) ?></span></td>
                        <td><?= esc($row['catatan_dpl'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
