<?php if (! empty($credentials)): ?>
<div class="card">
    <div class="card-head"><h2>Kredensial akun Dosen Pembimbing Lapangan — bagikan sekarang</h2></div>
    <p>
        Password hanya ditampilkan sekali di sini. Salin dan kirim ke <?= esc($credentials['nama'] ?? 'Dosen') ?>.
    </p>
    <div class="stat-row">
        <div class="stat">
            <div class="label">Nama</div>
            <div class="value"><?= esc($credentials['nama'] ?? '-') ?></div>
        </div>
        <div class="stat">
            <div class="label">Username</div>
            <div class="value font-mono"><?= esc($credentials['username'] ?? '-') ?></div>
        </div>
        <div class="stat">
            <div class="label">Password</div>
            <div class="value font-mono"><?= esc($credentials['password'] ?? '-') ?></div>
        </div>
    </div>
    <p class="field-hint">Login: <code>/login</code> → panel Dosen Pembimbing Lapangan otomatis setelah autentikasi.</p>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-head">
        <h2>Kelola Data Dosen Pembimbing Lapangan</h2>
        <a href="<?= site_url('admin/dpl/create') ?>" class="btn btn-primary btn-sm">+ Buat akun DPL</a>
    </div>
    <p>
        Admin membuat akun Dosen Pembimbing Lapangan, lalu membagikan username &amp; password. DPL hanya perlu login.
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
                <tr>
                    <td colspan="7">
                        <div class="flex flex-col items-center justify-center py-14 px-6 text-center">
                            <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-2xl bg-slate-100 text-slate-300 dark:bg-slate-800 dark:text-slate-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" class="h-8 w-8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 3.741-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/>
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Belum ada Dosen Pembimbing</p>
                            <p class="mt-1 text-xs text-slate-400">Buat akun DPL lalu assign ke kelompok KKN</p>
                        </div>
                    </td>
                </tr>
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
                            <form method="post" action="<?= site_url('admin/dpl/' . $row['id'] . '/delete') ?>" data-confirm="Hapus akun DPL ini? Akun tidak dapat dipulihkan dari halaman ini.">
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
