<div class="card">
    <div class="card-head"><h2>Semua laporan</h2></div>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Nama Kelompok</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>File</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($laporan)): ?>
                <tr>
                    <td colspan="6">
                        <div class="flex flex-col items-center justify-center py-14 px-6 text-center">
                            <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Belum ada laporan</p>
                            <p class="mt-1 text-xs text-slate-400">Laporan dari mahasiswa akan muncul di sini</p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($laporan as $row): ?>
                    <tr>
                        <td><?= esc($row['judul']) ?></td>
                        <td><?= esc($row['nama_kelompok'] ?? '-') ?></td>
                        <td><span class="<?= stempel_class($row['status']) ?>"><?= stempel_label($row['status']) ?></span></td>
                        <td><?= format_tanggal($row['created_at'] ?? null) ?></td>
                        <td><?= view('partials/stored-files', ['value' => $row['file_laporan'] ?? null]) ?></td>
                        <td class="actions">
                            <form method="post" action="<?= site_url('admin/laporan/' . (int) $row['id'] . '/delete') ?>" data-confirm="Hapus laporan ini?">
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
