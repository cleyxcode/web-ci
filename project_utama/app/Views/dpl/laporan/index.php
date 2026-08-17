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
                    <th>Mahasiswa</th>
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
                            <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-2xl bg-slate-100 text-slate-300 dark:bg-slate-800 dark:text-slate-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" class="h-8 w-8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                                </svg>
                            </div>
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
                        <td>
                            <?php if (! empty($row['file_laporan'])): ?>
                                <a href="<?= base_url('uploads/' . $row['file_laporan']) ?>" target="_blank">PDF</a>
                            <?php else: ?>-<?php endif; ?>
                        </td>
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
                                <span><?= esc($row['catatan_dpl'] ?? 'Selesai') ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
