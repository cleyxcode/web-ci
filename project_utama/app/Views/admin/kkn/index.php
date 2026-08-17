<div class="card">
    <div class="card-head">
        <h2>Kelompok KKN</h2>
        <a href="<?= site_url('admin/kkn/create') ?>" class="btn btn-primary btn-sm">+ Tambah kelompok</a>
    </div>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>Nama kelompok</th>
                    <th>Ketua</th>
                    <th>DPL</th>
                    <th>Lokasi</th>
                    <th>GPS</th>
                    <th>Anggota</th>
                    <th>Periode</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($kelompok)): ?>
                <tr>
                    <td colspan="8">
                        <div class="flex flex-col items-center justify-center py-14 px-6 text-center">
                            <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-2xl bg-slate-100 text-slate-300 dark:bg-slate-800 dark:text-slate-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" class="h-8 w-8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Belum ada kelompok KKN</p>
                            <p class="mt-1 text-xs text-slate-400">Buat kelompok baru lalu tempatkan mahasiswa ke dalamnya</p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($kelompok as $row): ?>
                    <tr>
                        <td>
                            <a href="<?= site_url('admin/kkn/' . $row['id']) ?>"><?= esc($row['nama_kelompok']) ?></a>
                        </td>
                        <td><?= esc($row['nama_ketua'] ?? '—') ?></td>
                        <td><?= esc($row['nama_dpl'] ?? '-') ?></td>
                        <td><?= esc(format_alamat($row)) ?></td>
                        <td><?= ! empty($row['latitude']) ? '✓' : '—' ?></td>
                        <td><span class="badge-count"><?= (int) ($row['jumlah_anggota'] ?? 0) ?> mhs</span></td>
                        <td class="font-mono"><?= esc($row['periode'] ?? '-') ?></td>
                        <td class="actions">
                            <a class="btn btn-primary btn-sm" href="<?= site_url('admin/kkn/' . $row['id']) ?>">Kelola</a>
                            <a class="btn btn-secondary btn-sm" href="<?= site_url('admin/kkn/' . $row['id'] . '/edit') ?>">Edit</a>
                            <form method="post" action="<?= site_url('admin/kkn/' . $row['id'] . '/delete') ?>" data-confirm="Hapus kelompok ini? Mahasiswa akan dikeluarkan dari kelompok.">
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
