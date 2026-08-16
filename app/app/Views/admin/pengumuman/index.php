<div class="card">
    <div class="card-head">
        <h2>Pengumuman</h2>
        <a href="<?= site_url('admin/pengumuman/create') ?>" class="btn btn-primary btn-sm">+ Buat</a>
    </div>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Isi</th>
                    <th>Tanggal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($pengumuman)): ?>
                <tr>
                    <td colspan="4">
                        <div class="flex flex-col items-center justify-center py-14 px-6 text-center">
                            <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-2xl bg-slate-100 text-slate-300 dark:bg-slate-800 dark:text-slate-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" class="h-8 w-8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 1 1 0-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 0 1-1.44-4.282m3.102.069a18.03 18.03 0 0 1-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 0 1 8.835 2.535M10.34 6.66a23.847 23.847 0 0 1 8.835-2.535m0 0A23.74 23.74 0 0 1 18.795 3m.38 1.125a23.91 23.91 0 0 1 1.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 0 0 1.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 0 1 0 3.46"/>
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Belum ada pengumuman</p>
                            <p class="mt-1 text-xs text-slate-400">Buat pengumuman untuk disampaikan kepada mahasiswa</p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($pengumuman as $row): ?>
                    <tr>
                        <td><?= esc($row['judul']) ?></td>
                        <td><?= esc(mb_strimwidth($row['isi'], 0, 80, '…')) ?></td>
                        <td><?= format_tanggal($row['created_at'] ?? null) ?></td>
                        <td>
                            <form method="post" action="<?= site_url('admin/pengumuman/' . $row['id'] . '/delete') ?>" data-confirm="Hapus pengumuman ini?">
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
