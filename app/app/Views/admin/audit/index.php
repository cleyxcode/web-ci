<div class="card">
    <div class="card-head">
        <h2>Audit trail validasi</h2>
        <form method="get" class="filter-bar">
            <select name="aksi" onchange="this.form.submit()">
                <option value="">Semua aksi</option>
                <?php foreach (['divalidasi', 'diterima', 'ditolak', 'publish_nilai', 'update_nilai', 'set_gps', 'set_ketua', 'export'] as $opt): ?>
                    <option value="<?= $opt ?>" <?= ($aksi ?? '') === $opt ? 'selected' : '' ?>><?= esc($opt) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="entitas" onchange="this.form.submit()">
                <option value="">Semua entitas</option>
                <?php foreach (['logbook', 'laporan', 'penilaian', 'kelompok_kkn', 'mahasiswa'] as $opt): ?>
                    <option value="<?= $opt ?>" <?= ($entitas ?? '') === $opt ? 'selected' : '' ?>><?= esc($opt) ?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Pengguna</th>
                    <th>Role</th>
                    <th>Aksi</th>
                    <th>Entitas</th>
                    <th>Deskripsi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($logs)): ?>
                <tr>
                    <td colspan="6">
                        <div class="flex flex-col items-center justify-center py-14 px-6 text-center">
                            <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-2xl bg-slate-100 text-slate-300 dark:bg-slate-800 dark:text-slate-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" class="h-8 w-8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Belum ada catatan audit</p>
                            <p class="mt-1 text-xs text-slate-400">Aktivitas sistem akan tercatat di sini</p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td class="font-mono"><?= esc($log['created_at'] ?? '-') ?></td>
                        <td><?= esc($log['user_nama'] ?? '-') ?></td>
                        <td><?= esc($log['user_role'] ?? '-') ?></td>
                        <td><span class="badge-count"><?= esc($log['aksi']) ?></span></td>
                        <td class="font-mono"><?= esc($log['entitas']) ?><?= $log['entitas_id'] ? ' #' . (int) $log['entitas_id'] : '' ?></td>
                        <td><?= esc($log['deskripsi']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
