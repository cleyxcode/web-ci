<div class="space-y-6">
    <section class="hero-strip">
        <div><p class="periode">Monitoring kegiatan</p><h2>Logbook Mahasiswa</h2><span>Admin dapat melihat seluruh kegiatan dan status validasi logbook. Validasi tetap dilakukan oleh DPL.</span></div>
    </section>

    <section class="card">
        <div class="card-head">
            <div><h2>Daftar logbook</h2><p class="mt-1 text-sm text-slate-500 dark:text-slate-400"><?= count($logbooks ?? []) ?> data ditampilkan</p></div>
            <form method="get" class="flex items-center gap-2">
                <label for="status" class="sr-only">Filter status</label>
                <select id="status" name="status" onchange="this.form.submit()">
                    <option value="">Semua status</option>
                    <option value="menunggu" <?= ($filterStatus ?? '') === 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                    <option value="divalidasi" <?= ($filterStatus ?? '') === 'divalidasi' ? 'selected' : '' ?>>Divalidasi</option>
                    <option value="ditolak" <?= ($filterStatus ?? '') === 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                </select>
            </form>
        </div>
        <div class="table-wrap responsive-table"><table class="data w-full text-left"><thead><tr><th>Tanggal</th><th>Mahasiswa</th><th>Kelompok</th><th>DPL</th><th>Kegiatan</th><th>Dokumentasi</th><th>Status</th></tr></thead><tbody>
        <?php if (empty($logbooks)): ?><tr><td colspan="7"><div class="empty">Belum ada logbook.</div></td></tr><?php else: foreach ($logbooks as $row): ?><tr>
            <td data-label="Tanggal"><?= format_tanggal($row['tanggal'] ?? null) ?></td>
            <td data-label="Mahasiswa"><strong><?= esc($row['nama_mahasiswa'] ?? '-') ?></strong><small class="block text-xs text-slate-400"><?= esc($row['npm'] ?? '-') ?></small></td>
            <td data-label="Kelompok"><?= esc($row['nama_kelompok'] ?? '-') ?></td>
            <td data-label="DPL"><?= esc($row['nama_dpl'] ?? '-') ?></td>
            <td data-label="Kegiatan" class="max-w-sm"><?= esc($row['kegiatan'] ?? '-') ?></td>
            <td data-label="Dokumentasi"><?php if (! empty($row['dokumentasi'])): ?><?php foreach (stored_files($row['dokumentasi']) as $file): ?><a class="mr-2" href="<?= base_url('uploads/' . $file) ?>" target="_blank">Lihat</a><?php endforeach; ?><?php else: ?>-<?php endif; ?></td>
            <td data-label="Status"><span class="<?= stempel_class($row['status'] ?? null) ?>"><?= stempel_label($row['status'] ?? null) ?></span></td>
        </tr><?php endforeach; endif; ?></tbody></table></div>
    </section>
</div>
