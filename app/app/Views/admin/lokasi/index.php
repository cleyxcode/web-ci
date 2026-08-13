<div class="card">
    <div class="card-head">
        <h2>Lokasi KKN</h2>
        <a href="<?= site_url('admin/lokasi/create') ?>" class="btn btn-primary btn-sm">+ Tambah</a>
    </div>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>Desa</th>
                    <th>Kecamatan</th>
                    <th>Kabupaten</th>
                    <th>Alamat lengkap</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($lokasi)): ?>
                <tr><td colspan="5" class="empty">Belum ada lokasi.</td></tr>
            <?php else: ?>
                <?php foreach ($lokasi as $row): ?>
                    <tr>
                        <td><?= esc($row['nama_desa']) ?></td>
                        <td><?= esc($row['kecamatan'] ?? '-') ?></td>
                        <td><?= esc($row['kabupaten'] ?? '-') ?></td>
                        <td><?= esc(format_alamat($row)) ?></td>
                        <td class="actions">
                            <a class="btn btn-secondary btn-sm" href="<?= site_url('admin/lokasi/' . $row['id'] . '/edit') ?>">Edit</a>
                            <form method="post" action="<?= site_url('admin/lokasi/' . $row['id'] . '/delete') ?>" onsubmit="return confirm('Hapus lokasi ini?')">
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
