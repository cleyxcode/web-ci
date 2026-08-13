<div class="card">
    <div class="card-head">
        <h2>Kelompok KKN</h2>
        <a href="<?= site_url('admin/kkn/create') ?>" class="btn btn-primary btn-sm">+ Tambah kelompok</a>
    </div>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>Nama kelompok</th>
                    <th>Ketua</th>
                    <th>DPL</th>
                    <th>Dosen pendamping</th>
                    <th>Lokasi</th>
                    <th>GPS</th>
                    <th>Anggota</th>
                    <th>Periode</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($kelompok)): ?>
                <tr><td colspan="9" class="empty">Belum ada kelompok. Buat kelompok lalu tempatkan mahasiswa.</td></tr>
            <?php else: ?>
                <?php foreach ($kelompok as $row): ?>
                    <tr>
                        <td>
                            <a href="<?= site_url('admin/kkn/' . $row['id']) ?>" style="font-weight:600"><?= esc($row['nama_kelompok']) ?></a>
                        </td>
                        <td><?= esc($row['nama_ketua'] ?? '—') ?></td>
                        <td><?= esc($row['nama_dpl'] ?? '-') ?></td>
                        <td><?= esc($row['dosen_pendamping'] ?? '-') ?></td>
                        <td><?= esc(format_alamat($row)) ?></td>
                        <td><?= ! empty($row['latitude']) ? '✓' : '—' ?></td>
                        <td><span class="badge-count"><?= (int) ($row['jumlah_anggota'] ?? 0) ?> mhs</span></td>
                        <td class="font-mono"><?= esc($row['periode'] ?? '-') ?></td>
                        <td class="actions">
                            <a class="btn btn-primary btn-sm" href="<?= site_url('admin/kkn/' . $row['id']) ?>">Kelola</a>
                            <a class="btn btn-secondary btn-sm" href="<?= site_url('admin/kkn/' . $row['id'] . '/edit') ?>">Edit</a>
                            <form method="post" action="<?= site_url('admin/kkn/' . $row['id'] . '/delete') ?>" onsubmit="return confirm('Hapus kelompok ini? Mahasiswa akan dikeluarkan.')">
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
