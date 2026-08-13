<div class="card">
    <div class="card-head">
        <h2>Daftar mahasiswa</h2>
        <a href="<?= site_url('admin/mahasiswa/create') ?>" class="btn btn-primary btn-sm">+ Tambah</a>
    </div>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>NPM</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Prodi</th>
                    <th>Kelompok</th>
                    <th>HP</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($mahasiswa)): ?>
                <tr><td colspan="7" class="empty">Belum ada mahasiswa.</td></tr>
            <?php else: ?>
                <?php foreach ($mahasiswa as $row): ?>
                    <tr>
                        <td class="font-mono"><?= esc($row['npm']) ?></td>
                        <td><?= esc($row['nama']) ?></td>
                        <td><?= esc($row['email'] ?? '-') ?></td>
                        <td><?= esc($row['prodi'] ?? '-') ?></td>
                        <td><?= esc($row['nama_kelompok'] ?? '-') ?></td>
                        <td class="font-mono"><?= esc($row['no_hp'] ?? '-') ?></td>
                        <td class="actions">
                            <a class="btn btn-secondary btn-sm" href="<?= site_url('admin/mahasiswa/' . $row['id'] . '/edit') ?>">Edit</a>
                            <form method="post" action="<?= site_url('admin/mahasiswa/' . $row['id'] . '/delete') ?>" onsubmit="return confirm('Hapus mahasiswa ini?')">
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
