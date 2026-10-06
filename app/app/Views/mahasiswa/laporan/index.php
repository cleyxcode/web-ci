<div class="card">
    <div class="card-head">
        <h2>Laporan kegiatan kelompok</h2>
        <?php if ($is_ketua): ?>
            <a href="<?= site_url('mahasiswa/laporan/create') ?>" class="btn btn-primary btn-sm">+ Upload</a>
        <?php endif; ?>
    </div>

    <?php if (!$is_ketua): ?>
        <div class="alert alert-info" style="margin: 0 1.25rem 1rem; padding: .75rem 1rem; background: #eff6ff; border-left: 4px solid #3b82f6; border-radius: .5rem; font-size: .875rem; color: #1d4ed8;">
            Hanya ketua kelompok yang dapat mengupload / mengedit / menghapus laporan (termasuk yang sudah diterima). Laporan di bawah merupakan laporan kelompok Anda.
        </div>
    <?php endif; ?>

    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Deskripsi</th>
                    <th>File</th>
                    <th>Status</th>
                    <th>Catatan DPL</th>
                    <?php if ($is_ketua): ?>
                        <th>Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($laporan)): ?>
                <tr>
                    <td colspan="<?= $is_ketua ? 6 : 5 ?>">
                        <div class="flex flex-col items-center justify-center py-14 px-6 text-center">
                            <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-2xl bg-slate-100 text-slate-300 dark:bg-slate-800 dark:text-slate-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" class="h-8 w-8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Belum ada laporan kelompok</p>
                            <?php if ($is_ketua): ?>
                                <p class="mt-1 text-xs text-slate-400">Upload laporan kegiatan KKN Tematik kelompok Anda di sini</p>
                                <a href="<?= site_url('mahasiswa/laporan/create') ?>" class="mt-4 inline-flex items-center gap-1.5 rounded-xl bg-violet-600 px-4 py-2 text-xs font-extrabold text-white shadow-sm hover:bg-violet-700 transition">
                                    Upload Laporan Pertama
                                </a>
                            <?php else: ?>
                                <p class="mt-1 text-xs text-slate-400">Ketua kelompok belum mengupload laporan</p>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($laporan as $row): ?>
                    <tr>
                        <td><?= esc($row['judul']) ?></td>
                        <td><?= esc(mb_strimwidth($row['deskripsi'] ?? '', 0, 60, '…')) ?></td>
                        <td><?= view('partials/stored-files', ['value' => $row['file_laporan'] ?? null]) ?></td>
                        <td><span class="<?= stempel_class($row['status']) ?>"><?= stempel_label($row['status']) ?></span></td>
                        <td><?= esc($row['catatan_dpl'] ?? '-') ?></td>
                        <?php if ($is_ketua): ?>
                            <td class="actions">
                                <?php if (($row['status'] ?? 'menunggu') === 'menunggu'): ?>
                                    <a href="<?= site_url('mahasiswa/laporan/' . (int) $row['id'] . '/edit') ?>" class="btn btn-secondary btn-sm">Edit</a>
                                <?php endif; ?>
                                <form method="post" action="<?= site_url('mahasiswa/laporan/' . (int) $row['id'] . '/delete') ?>" data-confirm="Hapus laporan ini? File upload ikut terhapus.">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
