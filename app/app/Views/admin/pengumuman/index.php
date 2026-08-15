<div class="card">
    <div class="card-head">
        <h2>Pengumuman</h2>
        <a href="<?= site_url('admin/pengumuman/create') ?>" class="btn btn-primary btn-sm">+ Buat</a>
    </div>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Isi</th>
                    <th>Tanggal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($pengumuman)): ?>
                <tr><td colspan="4" class="empty">Belum ada pengumuman.</td></tr>
            <?php else: ?>
                <?php foreach ($pengumuman as $row): ?>
                    <tr>
                        <td><?= esc($row['judul']) ?></td>
                        <td><?= esc(mb_strimwidth($row['isi'], 0, 80, '…')) ?></td>
                        <td><?= format_tanggal($row['created_at'] ?? null) ?></td>
                        <td>
                            <form method="post" action="<?= site_url('admin/pengumuman/' . $row['id'] . '/delete') ?>" data-confirm="Hapus pengumuman ini?">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
