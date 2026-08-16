<?php
$allMhs = $mahasiswa ?? [];
?>
<div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div class="min-w-0">
        <p class="mb-1 text-[11px] font-extrabold uppercase tracking-[0.16em] text-violet-600 dark:text-violet-300">Direktori akademik</p>
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">Manajemen Mahasiswa</h1>
        <p class="mt-1 text-sm font-semibold text-slate-500 dark:text-slate-400">Kelola akun, kelompok KKN, dan kontak mahasiswa dalam satu tempat.</p>
    </div>
    <a href="<?= site_url('admin/mahasiswa/create') ?>" class="btn btn-primary w-full gap-2 sm:w-auto">
        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
        <span>Tambah Mahasiswa</span>
    </a>
</div>

<!-- Search & Filter -->
<div class="card mb-5 overflow-hidden border-violet-100/80 p-0 dark:border-violet-900/50">
    <div class="border-b border-slate-100 bg-gradient-to-r from-violet-50/80 to-indigo-50/50 px-4 py-4 dark:border-slate-800 dark:from-violet-950/30 dark:to-slate-900">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-sm font-extrabold text-slate-900 dark:text-white">Cari dan saring mahasiswa</h2>
                <p class="mt-0.5 text-xs font-semibold text-slate-500 dark:text-slate-400">Gunakan nama, NPM, email, prodi, atau kelompok.</p>
            </div>
            <span class="hidden rounded-full bg-white/80 px-3 py-1 text-[11px] font-extrabold text-violet-700 shadow-sm sm:inline-flex dark:bg-slate-800 dark:text-violet-300">Filter cepat</span>
        </div>
    </div>
    <div class="grid gap-3 p-4 lg:grid-cols-[minmax(0,1fr)_minmax(220px,280px)_auto] lg:items-end">
        <label class="block min-w-0">
            <span class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">Pencarian</span>
            <span class="relative block">
                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="m21 21-4.35-4.35"/></svg>
                <input type="search" id="mhs-search" class="!pl-10" placeholder="Cari nama, NPM, email, atau prodi..." autocomplete="off" aria-label="Cari mahasiswa">
            </span>
        </label>
        <label class="block min-w-0">
            <span class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">Kelompok</span>
            <select id="mhs-filter-kelompok" aria-label="Filter kelompok mahasiswa">
                <option value="">Semua kelompok</option>
                <?php foreach ($kelompok ?? [] as $k): ?>
                    <option value="<?= esc($k['nama_kelompok']) ?>"><?= esc($k['nama_kelompok']) ?></option>
                <?php endforeach; ?>
                <option value="-">Belum ada kelompok</option>
            </select>
        </label>
        <div id="mhs-count-info" class="min-h-10 rounded-xl bg-slate-50 px-3 py-2.5 text-center text-xs font-extrabold text-slate-500 dark:bg-slate-800/70 dark:text-slate-400 lg:min-w-32"><?= count($allMhs) ?> mahasiswa</div>
    </div>
</div>

<!-- Table -->
<div class="card overflow-hidden p-0">
    <div class="flex items-center justify-between border-b border-slate-100 px-4 py-4 dark:border-slate-800 sm:px-5">
        <div>
            <h2 class="text-sm font-extrabold text-slate-900 dark:text-white">Daftar mahasiswa</h2>
            <p class="mt-0.5 text-xs font-semibold text-slate-400"><?= count($allMhs) ?> akun terdaftar</p>
        </div>
        <span class="grid h-9 w-9 place-items-center rounded-xl bg-violet-50 text-violet-600 dark:bg-violet-950/40 dark:text-violet-300" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm13 10v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </span>
    </div>
    <div class="table-wrap px-3 pb-3 sm:px-5 sm:pb-5">
        <table class="data" id="mhs-table">
            <thead>
                <tr>
                    <th></th>
                    <th>Nama & Akun</th>
                    <th>NPM</th>
                    <th>Program Studi</th>
                    <th>Kelompok KKN</th>
                    <th>Kontak</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($allMhs)): ?>
                <tr id="mhs-empty-default">
                    <td colspan="7">
                        <div class="flex flex-col items-center justify-center py-14 px-6 text-center">
                            <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-2xl bg-slate-100 text-slate-300 dark:bg-slate-800 dark:text-slate-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" class="h-8 w-8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm13 10v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Belum ada data mahasiswa</p>
                            <p class="mt-1 text-xs text-slate-400">Import atau tambahkan akun mahasiswa untuk mulai</p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($allMhs as $row): ?>
                    <?php
                    $initial     = strtoupper(mb_substr($row['nama'] ?? 'M', 0, 1));
                    $hasKelompok = ! empty($row['nama_kelompok']);
                    $npm         = $row['npm'] ?? '';
                    $npmTemp     = empty($npm) || str_starts_with($npm, 'TEMP_');
                    ?>
                    <tr class="mhs-row" data-nama="<?= strtolower(esc($row['nama'] ?? '')) ?>"
                        data-npm="<?= strtolower(esc($npm)) ?>"
                        data-email="<?= strtolower(esc($row['email'] ?? '')) ?>"
                        data-prodi="<?= strtolower(esc($row['prodi'] ?? '')) ?>"
                        data-kelompok="<?= esc($hasKelompok ? $row['nama_kelompok'] : '-') ?>">
                        <td data-label="Profil">
                            <div>
                                <?= esc($initial) ?>
                            </div>
                        </td>
                        <td data-label="Nama & akun">
                            <div><?= esc($row['nama'] ?? '-') ?></div>
                            <div><?= esc($row['email'] ?? '-') ?></div>
                        </td>
                        <td data-label="NPM">
                            <?php if ($npmTemp): ?>
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>
                                    Belum diisi
                                </span>
                            <?php else: ?>
                                <span class="font-mono"><?= esc($npm) ?></span>
                            <?php endif; ?>
                        </td>
                        <td data-label="Program studi"><?= esc($row['prodi'] ?? '-') ?></td>
                        <td data-label="Kelompok KKN">
                            <?php if ($hasKelompok): ?>
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="12"/></svg>
                                    <?= esc($row['nama_kelompok']) ?>
                                </span>
                            <?php else: ?>
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="12"/></svg>
                                    Belum ditempatkan
                                </span>
                            <?php endif; ?>
                        </td>
                        <td data-label="Kontak">
                            <?php if (! empty($row['no_hp'])): ?>
                                <a href="https://wa.me/<?= preg_replace('/\D/', '', $row['no_hp']) ?>" target="_blank" rel="noopener"
                                  >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M20.52 3.45A11.82 11.82 0 0 0 12 0C5.37 0 0 5.37 0 12c0 2.11.55 4.16 1.6 5.97L0 24l6.2-1.57A11.94 11.94 0 0 0 12 24c6.63 0 12-5.37 12-12 0-3.2-1.25-6.22-3.48-8.55zM12 22c-1.85 0-3.66-.5-5.24-1.44l-.38-.22-3.88.98.99-3.76-.25-.4A9.96 9.96 0 0 1 2 12C2 6.48 6.48 2 12 2c2.67 0 5.18 1.04 7.07 2.93A9.93 9.93 0 0 1 22 12c0 5.52-4.48 10-10 10zm5.5-7.5c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.76.97-.93 1.17-.17.2-.35.22-.65.07-.3-.15-1.27-.47-2.42-1.5-.9-.8-1.5-1.78-1.67-2.08-.17-.3-.02-.46.13-.6.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.6-.92-2.2-.24-.57-.5-.5-.67-.5H7c-.2 0-.5.07-.77.37-.27.3-1 1-1 2.44s1.03 2.83 1.17 3.03c.15.2 2.02 3.1 4.9 4.35.68.3 1.22.47 1.63.6.69.22 1.31.19 1.81.12.55-.08 1.76-.72 2.01-1.42.25-.7.25-1.3.17-1.42-.07-.12-.27-.2-.57-.35z"/></svg>
                                    <?= esc($row['no_hp']) ?>
                                </a>
                            <?php else: ?>
                                <span>—</span>
                            <?php endif; ?>
                        </td>
                        <td data-label="Aksi">
                            <div>
                                <a href="<?= site_url('admin/mahasiswa/' . $row['id'] . '/edit') ?>" title="Edit akun mahasiswa" class="grid h-8 w-8 place-items-center rounded-lg bg-slate-100 text-slate-600 transition hover:bg-blue-100 hover:text-blue-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-5m-1.414-9.414a2 2 0 1 1 2.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form method="post" action="<?= site_url('admin/mahasiswa/' . $row['id'] . '/delete') ?>"
                                      data-confirm="Hapus akun mahasiswa <?= esc($row['nama'] ?? '', 'attr') ?>?">
                                    <?= csrf_field() ?>
                                    <button type="submit" title="Hapus mahasiswa" class="grid h-8 w-8 place-items-center rounded-lg bg-slate-100 text-slate-600 transition hover:bg-rose-100 hover:text-rose-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr id="mhs-no-result" class="hidden">
                    <td colspan="7">
                        <div class="flex flex-col items-center justify-center py-14 px-6 text-center">
                            <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-2xl bg-slate-100 text-slate-300 dark:bg-slate-800 dark:text-slate-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" class="h-8 w-8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Tidak ditemukan</p>
                            <p class="mt-1 text-xs text-slate-400">Tidak ada mahasiswa yang cocok dengan pencarian.</p>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
(function () {
    const searchEl   = document.getElementById('mhs-search');
    const filterEl   = document.getElementById('mhs-filter-kelompok');
    const countInfo  = document.getElementById('mhs-count-info');
    const rows       = document.querySelectorAll('.mhs-row');
    const noResult   = document.getElementById('mhs-no-result');
    const total      = rows.length;

    function applyFilter() {
        const q      = (searchEl?.value ?? '').toLowerCase().trim();
        const grp    = (filterEl?.value ?? '').toLowerCase();
        let visible  = 0;

        rows.forEach(row => {
            const nama    = row.dataset.nama    ?? '';
            const npm     = row.dataset.npm     ?? '';
            const email   = row.dataset.email   ?? '';
            const prodi   = row.dataset.prodi   ?? '';
            const kelompok = (row.dataset.kelompok ?? '').toLowerCase();

            const matchQ = !q || nama.includes(q) || npm.includes(q) || email.includes(q) || prodi.includes(q);
            const matchG = !grp || kelompok === grp || (grp === '-' && (kelompok === '-' || kelompok === ''));

            const show = matchQ && matchG;
            row.classList.toggle('hidden', !show);
            if (show) visible++;
        });

        if (noResult) noResult.classList.toggle('hidden', !(visible === 0 && total > 0));
        if (countInfo) {
            countInfo.textContent = (q || grp) ? `Menampilkan ${visible} dari ${total}` : `${total} mahasiswa`;
        }
    }

    searchEl?.addEventListener('input', applyFilter);
    filterEl?.addEventListener('change', applyFilter);
})();
</script>
