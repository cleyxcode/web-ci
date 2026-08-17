<div class="card">
    <div class="card-head">
        <h2>Antrian validasi logbook</h2>
        <a href="<?= site_url('dpl/monitoring') ?>" class="btn btn-secondary btn-sm">Lihat monitoring</a>
    </div>
    <form method="get" class="filter-bar">
        <div class="field">
            <label for="logbook-status">Tampilkan status</label>
            <select id="logbook-status" name="status">
                <option value="">Semua status</option>
                <option value="menunggu" <?= ($filterStatus ?? '') === 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                <option value="divalidasi" <?= ($filterStatus ?? '') === 'divalidasi' ? 'selected' : '' ?>>Divalidasi</option>
                <option value="ditolak" <?= ($filterStatus ?? '') === 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Terapkan</button>
        <?php if (! empty($filterStatus)): ?><a href="<?= site_url('dpl/logbook') ?>" class="btn btn-secondary btn-sm">Reset</a><?php endif; ?>
    </form>
    <div class="table-wrap responsive-table">
        <table class="data">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Mahasiswa</th>
                    <th>Kelompok</th>
                    <th>Kegiatan</th>
                    <th>Dok</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($logbooks)): ?>
                <tr>
                    <td colspan="7">
                        <div class="flex flex-col items-center justify-center py-14 px-6 text-center">
                            <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-2xl bg-slate-100 text-slate-300 dark:bg-slate-800 dark:text-slate-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" class="h-8 w-8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Tidak ada logbook</p>
                            <p class="mt-1 text-xs text-slate-400">Mahasiswa bimbingan Anda belum mencatat kegiatan</p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($logbooks as $row): ?>
                    <tr>
                        <td data-label="Tanggal"><?= format_tanggal($row['tanggal'] ?? null) ?></td>
                        <td data-label="Mahasiswa">
                            <strong><?= esc($row['nama_mahasiswa'] ?? $row['nama'] ?? '-') ?></strong><br>
                            <span class="font-mono"><?= esc($row['npm'] ?? '') ?></span>
                        </td>
                        <td data-label="Kelompok">
                            <?= esc($row['nama_kelompok'] ?? '-') ?><br>
                            <span class="field-hint"><?= esc(format_alamat($row)) ?></span>
                        </td>
                        <td data-label="Kegiatan">
                            <?= esc($row['kegiatan']) ?>
                            <?php if (! empty($row['catatan_dpl'])): ?>
                                <div class="field-hint">Catatan: <?= esc($row['catatan_dpl']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td data-label="Dok">
                            <?php if (! empty($row['dokumentasi'])): ?>
                                <a href="<?= base_url('uploads/' . $row['dokumentasi']) ?>" target="_blank">Lihat</a>
                            <?php else: ?>-<?php endif; ?>
                        </td>
                        <td data-label="Status"><span class="<?= stempel_class($row['status']) ?>"><?= stempel_label($row['status']) ?></span></td>
                        <td data-label="Aksi">
                            <?php if ($row['status'] === 'menunggu'): ?>
                                <form method="post" action="<?= site_url('dpl/logbook/' . $row['id'] . '/proses') ?>">
                                    <?= csrf_field() ?>
                                    <input type="text" name="catatan_dpl" placeholder="Catatan (opsional)">
                                    <div class="actions">
                                        <button type="submit" name="action" value="validasi" class="btn btn-success btn-sm">Validasi</button>
                                        <button type="submit" name="action" value="tolak" class="btn btn-danger btn-sm">Tolak</button>
                                    </div>
                                </form>
                            <?php else: ?>
                                <span>Selesai</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
