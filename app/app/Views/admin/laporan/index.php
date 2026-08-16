<div class="card">
    <div class="card-head"><h2>Semua laporan</h2></div>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Mahasiswa</th>
                    <th>NPM</th>
                    <th>Status</th>
                    <th>File</th>
                    <th>Tanggal</th>
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
                            <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Belum ada laporan</p>
                            <p class="mt-1 text-xs text-slate-400">Laporan dari mahasiswa akan muncul di sini</p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($laporan as $row): ?>
                    <tr>
                        <td><?= esc($row['judul']) ?></td>
                        <td><?= esc($row['nama_mahasiswa'] ?? $row['nama'] ?? '-') ?></td>
                        <td class="font-mono"><?= esc($row['npm'] ?? '-') ?></td>
                        <td><span class="<?= stempel_class($row['status']) ?>"><?= stempel_label($row['status']) ?></span></td>
                        <td>
                            <?php if (! empty($row['file_laporan'])): ?>
                                <a href="<?= base_url('uploads/' . $row['file_laporan']) ?>" target="_blank">PDF</a>
                            <?php else: ?>-<?php endif; ?>
                        </td>
                        <td><?= format_tanggal($row['created_at'] ?? null) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
