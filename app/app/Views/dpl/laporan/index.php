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
                    <th>Ketua Kelompok</th>
                    <th>Kelompok</th>
                    <th>File</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($laporan)): ?>
                <tr>
                    <td colspan="6">
                        <div class="flex flex-col items-center justify-center py-14 px-6 text-center">
                            <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Tidak ada laporan</p>
                            <p class="mt-1 text-xs text-slate-400">Mahasiswa bimbingan Anda belum mengupload laporan</p>
                        </div>
                    </td>
                </tr>
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
                        <td><?= view('partials/stored-files', ['value' => $row['file_laporan'] ?? null, 'empty' => 'Belum ada file']) ?></td>
                        <td><span class="<?= stempel_class($row['status']) ?>"><?= stempel_label($row['status']) ?></span></td>
                        <td>
                            <?php if ($row['status'] === 'menunggu'): ?>
                                <form method="post" action="<?= site_url('dpl/laporan/' . $row['id'] . '/review') ?>">
                                    <?= csrf_field() ?>
                                    <input type="text" name="catatan_dpl" placeholder="Catatan (opsional)">
                                    <div class="actions">
                                        <button type="submit" name="action" value="terima" class="btn btn-success btn-sm">Terima</button>
                                        <button type="submit" name="action" value="tolak" class="btn btn-danger btn-sm">Tolak</button>
                                    </div>
                                </form>
                            <?php else: ?>
                                <div class="field-hint mb-2"><?= esc($row['catatan_dpl'] ?? 'Selesai') ?></div>
                            <?php endif; ?>
                            <form method="post" action="<?= site_url('dpl/laporan/' . (int) $row['id'] . '/delete') ?>" data-confirm="Hapus laporan ini? File upload ikut terhapus." class="mt-2">
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
