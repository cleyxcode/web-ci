<?php if (! empty($credentials)): ?>
<div class="card" style="margin-bottom:16px;border-color:var(--hijau-hutan,#2D7A4F)">
    <div class="card-head"><h2>Kredensial akun DPL — bagikan sekarang</h2></div>
    <p style="margin:0 0 12px;color:var(--abu-karang);font-size:0.9rem">
        Password hanya ditampilkan sekali di sini. Salin dan kirim ke <?= esc($credentials['nama'] ?? 'DPL') ?>.
    </p>
    <div class="stat-row">
        <div class="stat">
            <div class="label">Nama</div>
            <div class="value" style="font-size:1rem"><?= esc($credentials['nama'] ?? '-') ?></div>
        </div>
        <div class="stat">
            <div class="label">Username</div>
            <div class="value font-mono" style="font-size:1rem"><?= esc($credentials['username'] ?? '-') ?></div>
        </div>
        <div class="stat">
            <div class="label">Password</div>
            <div class="value font-mono" style="font-size:1rem"><?= esc($credentials['password'] ?? '-') ?></div>
        </div>
    </div>
    <p class="field-hint" style="margin-top:8px">Login: <code>/login</code> → panel DPL otomatis setelah autentikasi.</p>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-head">
        <h2>Kelola Data DPL</h2>
        <a href="<?= site_url('admin/dpl/create') ?>" class="btn btn-primary btn-sm">+ Buat akun DPL</a>
    </div>
    <p style="margin:0 0 12px;color:var(--abu-karang);font-size:0.9rem">
        Admin membuat akun DPL, lalu membagikan username &amp; password. DPL hanya perlu login.
    </p>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>NIDN</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Prodi</th>
                    <th>HP</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($dpl)): ?>
                <tr><td colspan="7" class="empty">Belum ada DPL. Buat akun lalu assign ke kelompok KKN.</td></tr>
            <?php else: ?>
                <?php foreach ($dpl as $row): ?>
                    <tr>
                        <td class="font-mono"><?= esc($row['nidn']) ?></td>
                        <td><?= esc($row['nama']) ?></td>
                        <td class="font-mono"><?= esc($row['username'] ?? '-') ?></td>
                        <td><?= esc($row['email'] ?? '-') ?></td>
                        <td><?= esc($row['prodi'] ?? '-') ?></td>
                        <td class="font-mono"><?= esc($row['no_hp'] ?? '-') ?></td>
                        <td class="actions">
                            <a class="btn btn-secondary btn-sm" href="<?= site_url('admin/dpl/' . $row['id'] . '/edit') ?>">Edit</a>
                            <form method="post" action="<?= site_url('admin/dpl/' . $row['id'] . '/delete') ?>" onsubmit="return confirm('Hapus akun DPL ini?')">
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
