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
                <tr><td colspan="6" class="empty">Belum ada catatan audit.</td></tr>
            <?php else: ?>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td class="font-mono" style="white-space:nowrap"><?= esc($log['created_at'] ?? '-') ?></td>
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
