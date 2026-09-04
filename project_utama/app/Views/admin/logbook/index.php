<div class="admin-stat-grid">
    <div class="admin-stat-card tone-violet">
        <span class="admin-stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" class="admin-stat-icon_svg"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 4v15.5A2.5 2.5 0 0 1 6.5 17H20V4H6.5A2.5 2.5 0 0 0 4 6.5"/></svg>
        </span>
        <div>
            <span>Total Logbook</span>
            <strong><?= number_format($totalSemua) ?></strong>
            <small>semua entri</small>
        </div>
    </div>
    <div class="admin-stat-card tone-amber">
        <span class="admin-stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" class="admin-stat-icon_svg"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 1 1-20 0 10 10 0 0 1 20 0z"/></svg>
        </span>
        <div>
            <span>Menunggu Validasi</span>
            <strong><?= number_format($totalMenunggu) ?></strong>
            <small>perlu ditindaklanjuti</small>
        </div>
    </div>
    <div class="admin-stat-card tone-blue">
        <span class="admin-stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" class="admin-stat-icon_svg"><path stroke-linecap="round" stroke-linejoin="round" d="M9 11l3 3L22 4M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        </span>
        <div>
            <span>Tervalidasi</span>
            <strong><?= number_format($totalValidasi) ?></strong>
            <small>sudah disetujui DPL</small>
        </div>
    </div>
    <div class="admin-stat-card tone-green" style="--tone-icon-bg:rgb(254 226 226);--tone-icon-text:rgb(220 38 38)">
        <span class="admin-stat-icon" style="background:rgb(254 226 226);color:rgb(220 38 38)">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" class="admin-stat-icon_svg"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
        </span>
        <div>
            <span>Ditolak</span>
            <strong><?= number_format($totalDitolak) ?></strong>
            <small>dikembalikan DPL</small>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-head">
        <h2>Semua logbook kegiatan</h2>
    </div>

    <form method="get" class="filter-bar mb-4">
        <div class="field">
            <label for="lb-cari">Cari mahasiswa / kegiatan</label>
            <input type="text" id="lb-cari" name="cari" value="<?= esc($filterSearch) ?>" placeholder="Nama, NPM, atau kegiatan…" style="min-width:200px">
        </div>
        <div class="field">
            <label for="lb-status">Status</label>
            <select id="lb-status" name="status">
                <option value="">Semua status</option>
                <option value="menunggu" <?= $filterStatus === 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                <option value="divalidasi" <?= $filterStatus === 'divalidasi' ? 'selected' : '' ?>>Divalidasi</option>
                <option value="ditolak" <?= $filterStatus === 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
            </select>
        </div>
        <div class="field">
            <label for="lb-dari">Tanggal dari</label>
            <input type="date" id="lb-dari" name="dari" value="<?= esc($filterDari) ?>">
        </div>
        <div class="field">
            <label for="lb-sampai">Sampai</label>
            <input type="date" id="lb-sampai" name="sampai" value="<?= esc($filterSampai) ?>">
        </div>
        <div style="display:flex;gap:8px;align-items:flex-end;padding-bottom:1px">
            <button type="submit" class="btn btn-primary btn-sm">Terapkan</button>
            <?php if ($filterStatus !== '' || $filterSearch !== '' || $filterDari !== '' || $filterSampai !== ''): ?>
                <a href="<?= site_url('admin/logbook') ?>" class="btn btn-secondary btn-sm">Reset</a>
            <?php endif; ?>
        </div>
    </form>

    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Mahasiswa</th>
                    <th>Kelompok</th>
                    <th>Tanggal</th>
                    <th>Kegiatan</th>
                    <th>Lokasi</th>
                    <th>Dok</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($logbooks)): ?>
                <tr>
                    <td colspan="9">
                        <div class="flex flex-col items-center justify-center py-14 px-6 text-center">
                            <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-2xl bg-slate-100 text-slate-300 dark:bg-slate-800 dark:text-slate-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" class="h-8 w-8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Tidak ada logbook ditemukan</p>
                            <p class="mt-1 text-xs text-slate-400">Coba ubah filter atau cari dengan kata kunci lain</p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($logbooks as $i => $row): ?>
                    <tr>
                        <td class="text-slate-400 text-xs"><?= $i + 1 ?></td>
                        <td>
                            <strong><?= esc($row['nama_mahasiswa'] ?? '-') ?></strong><br>
                            <span class="text-xs text-slate-400 font-mono"><?= esc($row['npm'] ?? '-') ?></span>
                        </td>
                        <td>
                            <?php if (! empty($row['nama_kelompok'])): ?>
                                <span class="text-xs"><?= esc($row['nama_kelompok']) ?></span>
                            <?php else: ?>
                                <span class="text-slate-400">-</span>
                            <?php endif; ?>
                        </td>
                        <td><?= format_tanggal($row['tanggal'] ?? null) ?></td>
                        <td>
                            <?= esc(mb_strimwidth($row['kegiatan'] ?? '', 0, 60, '…')) ?>
                            <?php if (! empty($row['catatan_dpl'])): ?>
                                <div class="text-xs text-slate-400 mt-0.5">Catatan: <?= esc(mb_strimwidth($row['catatan_dpl'], 0, 40, '…')) ?></div>
                            <?php endif; ?>
                        </td>
                        <td><?= esc($row['lokasi_kegiatan'] ?? '-') ?></td>
                        <td>
                            <?php if (! empty($row['dokumentasi'])): ?>
                                <a href="<?= base_url('uploads/' . $row['dokumentasi']) ?>" target="_blank" class="text-violet-600 hover:underline text-xs font-bold">Lihat</a>
                            <?php else: ?>-<?php endif; ?>
                        </td>
                        <td><span class="<?= stempel_class($row['status']) ?>"><?= stempel_label($row['status']) ?></span></td>
                        <td class="actions">
                            <a href="<?= site_url('admin/logbook/' . (int) $row['id']) ?>" class="btn btn-secondary btn-sm">Detail</a>
                            <form method="post" action="<?= site_url('admin/logbook/' . (int) $row['id'] . '/delete') ?>" data-confirm="Hapus logbook ini permanen? File dokumentasi juga akan dihapus." data-confirm-danger="1" style="display:inline">
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
    <?php if (! empty($logbooks)): ?>
        <p class="text-xs text-slate-400 mt-3"><?= count($logbooks) ?> entri ditampilkan</p>
    <?php endif; ?>
</div>
