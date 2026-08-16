<div class="card">
    <div class="card-head">
        <h2>Logbook kegiatan</h2>
        <a href="<?= site_url('mahasiswa/logbook/create') ?>" class="btn btn-primary btn-sm">+ Tambah</a>
    </div>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Kegiatan</th>
                    <th>Lokasi</th>
                    <th>Dok</th>
                    <th>Status</th>
                    <th>Catatan DPL</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($logbooks)): ?>
                <tr>
                    <td colspan="6">
                        <div class="flex flex-col items-center justify-center py-14 px-6 text-center">
                            <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-2xl bg-slate-100 text-slate-300 dark:bg-slate-800 dark:text-slate-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" class="h-8 w-8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Belum ada logbook</p>
                            <p class="mt-1 text-xs text-slate-400">Mulai catat kegiatan KKN harian Anda</p>
                            <a href="<?= site_url('mahasiswa/logbook/create') ?>" class="mt-4 inline-flex items-center gap-1.5 rounded-xl bg-violet-600 px-4 py-2 text-xs font-extrabold text-white shadow-sm hover:bg-violet-700 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" class="h-3.5 w-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                </svg>
                                Tambah Logbook Pertama
                            </a>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($logbooks as $row): ?>
                    <tr>
                        <td><?= format_tanggal($row['tanggal'] ?? null) ?></td>
                        <td><?= esc($row['kegiatan']) ?></td>
                        <td><?= esc($row['lokasi_kegiatan'] ?? '-') ?></td>
                        <td>
                            <?php if (! empty($row['dokumentasi'])): ?>
                                <a href="<?= base_url('uploads/' . $row['dokumentasi']) ?>" target="_blank">Lihat</a>
                            <?php else: ?>-<?php endif; ?>
                        </td>
                        <td><span class="<?= stempel_class($row['status']) ?>"><?= stempel_label($row['status']) ?></span></td>
                        <td><?= esc($row['catatan_dpl'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
