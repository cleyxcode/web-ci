<div class="card">
    <div class="card-head">
        <h2>Daftar DPL</h2>
        <a href="<?= site_url('admin/dpl/create') ?>" class="btn btn-primary btn-sm">+ Tambah</a>
    </div>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>NIDN</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Prodi</th>
                    <th>HP</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($dpl)): ?>
                <tr><td colspan="6" class="empty">Belum ada DPL.</td></tr>
            <?php else: ?>
                <?php foreach ($dpl as $row): ?>
                    <tr>
                        <td class="font-mono"><?= esc($row['nidn']) ?></td>
                        <td><?= esc($row['nama']) ?></td>
                        <td><?= esc($row['email'] ?? '-') ?></td>
                        <td><?= esc($row['prodi'] ?? '-') ?></td>
                        <td class="font-mono"><?= esc($row['no_hp'] ?? '-') ?></td>
                        <td class="actions">
                            <a class="btn btn-secondary btn-sm" href="<?= site_url('admin/dpl/' . $row['id'] . '/edit') ?>">Edit</a>
                            <form method="post" action="<?= site_url('admin/dpl/' . $row['id'] . '/delete') ?>" onsubmit="return confirm('Hapus DPL ini?')">
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
