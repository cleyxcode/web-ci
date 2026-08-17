<div class="card">
    <div class="card-head">
        <h2>Lokasi KKN</h2>
        <a href="<?= site_url('admin/lokasi/create') ?>" class="btn btn-primary btn-sm">+ Tambah</a>
    </div>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>Desa</th>
                    <th>Kecamatan</th>
                    <th>Kabupaten</th>
                    <th>Alamat lengkap</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($lokasi)): ?>
                <tr>
                    <td colspan="5">
                        <div class="flex flex-col items-center justify-center py-14 px-6 text-center">
                            <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-2xl bg-slate-100 text-slate-300 dark:bg-slate-800 dark:text-slate-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" class="h-8 w-8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Belum ada lokasi</p>
                            <p class="mt-1 text-xs text-slate-400">Tambahkan lokasi desa tempat pelaksanaan KKN</p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($lokasi as $row): ?>
                    <tr>
                        <td><?= esc($row['nama_desa']) ?></td>
                        <td><?= esc($row['kecamatan'] ?? '-') ?></td>
                        <td><?= esc($row['kabupaten'] ?? '-') ?></td>
                        <td><?= esc(format_alamat($row)) ?></td>
                        <td class="actions">
                            <a class="btn btn-secondary btn-sm" href="<?= site_url('admin/lokasi/' . $row['id'] . '/edit') ?>">Edit</a>
                            <form method="post" action="<?= site_url('admin/lokasi/' . $row['id'] . '/delete') ?>" data-confirm="Hapus lokasi ini dari daftar?">
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
