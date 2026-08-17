<div class="card">
    <div class="card-head"><h2>Peta lokasi kelompok bimbingan</h2></div>
    <?= view('partials/map', [
        'mapId'   => 'map-dpl-monitor',
        'markers' => $petaKelompok ?? [],
        'zoom'    => 11,
        'class'   => 'map-box',
        'empty'   => 'Belum ada kelompok yang menetapkan lokasi GPS.',
    ]) ?>
</div>

<div class="card">
    <div class="card-head"><h2>Monitoring kegiatan mahasiswa</h2></div>
    <form method="get" class="filter-bar">
        <div class="field">
            <label for="monitoring-status">Filter status</label>
            <select id="monitoring-status" name="status">
                <option value="">Semua status</option>
                <option value="menunggu" <?= ($filterStatus ?? '') === 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                <option value="divalidasi" <?= ($filterStatus ?? '') === 'divalidasi' ? 'selected' : '' ?>>Divalidasi</option>
                <option value="ditolak" <?= ($filterStatus ?? '') === 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Terapkan</button>
        <?php if (! empty($filterStatus)): ?><a href="<?= site_url('dpl/monitoring') ?>" class="btn btn-secondary btn-sm">Reset</a><?php endif; ?>
    </form>
    <div class="table-wrap responsive-table">
        <table class="data">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Mahasiswa</th>
                    <th>Kelompok</th>
                    <th>Kegiatan</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($logbooks)): ?>
                <tr>
                    <td colspan="6">
                        <div class="flex flex-col items-center justify-center py-14 px-6 text-center">
                            <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-2xl bg-slate-100 text-slate-300 dark:bg-slate-800 dark:text-slate-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" class="h-8 w-8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Belum ada kegiatan</p>
                            <p class="mt-1 text-xs text-slate-400">Mahasiswa belum mencatat kegiatan KKN</p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($logbooks as $row): ?>
                    <tr>
                        <td data-label="Tanggal"><?= format_tanggal($row['tanggal'] ?? null) ?></td>
                        <td data-label="Mahasiswa"><?= esc($row['nama_mahasiswa'] ?? $row['nama'] ?? '-') ?></td>
                        <td data-label="Kelompok"><?= esc($row['nama_kelompok'] ?? '-') ?><br><span class="field-hint"><?= esc(format_alamat($row)) ?></span></td>
                        <td data-label="Kegiatan"><?= esc($row['kegiatan']) ?></td>
                        <td data-label="Lokasi"><?= esc($row['lokasi_kegiatan'] ?? '-') ?></td>
                        <td data-label="Status"><span class="<?= stempel_class($row['status']) ?>"><?= stempel_label($row['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
