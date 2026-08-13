<div class="card">
    <div class="card-head"><h2>Penilaian mahasiswa</h2></div>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>NPM</th>
                    <th>Nama</th>
                    <th>Prodi</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($mahasiswa)): ?>
                <tr><td colspan="4" class="empty">Belum ada mahasiswa bimbingan.</td></tr>
            <?php else: ?>
                <?php foreach ($mahasiswa as $row): ?>
                    <tr>
                        <td class="font-mono"><?= esc($row['npm']) ?></td>
                        <td><?= esc($row['nama']) ?></td>
                        <td><?= esc($row['prodi'] ?? '-') ?></td>
                        <td>
                            <a class="btn btn-primary btn-sm" href="<?= site_url('dpl/penilaian/' . $row['id']) ?>">Nilai</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
