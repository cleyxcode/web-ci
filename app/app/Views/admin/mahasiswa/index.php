<?php
$allMhs = $mahasiswa ?? [];
?>
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px">
    <div>
        <h1 style="font-size:1.35rem;font-weight:700;margin:0">Manajemen Akun Mahasiswa</h1>
        <p style="margin:4px 0 0;color:var(--muted,#888);font-size:.875rem"><?= count($allMhs) ?> mahasiswa terdaftar</p>
    </div>
    <a href="<?= site_url('admin/mahasiswa/create') ?>" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:7px">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
        Tambah Mahasiswa
    </a>
</div>

<!-- Search & Filter -->
<div class="card" style="margin-bottom:16px;padding:14px 18px">
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
        <div style="position:relative;flex:1;min-width:200px">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);opacity:.4"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="m21 21-4.35-4.35"/></svg>
            <input type="text" id="mhs-search" placeholder="Cari nama, NPM, email, prodi..." autocomplete="off"
                style="width:100%;padding:8px 12px 8px 34px;border:1px solid var(--border,#e0dbd4);border-radius:8px;font-size:.875rem;background:var(--bg,#fff);color:inherit;box-sizing:border-box">
        </div>
        <select id="mhs-filter-kelompok" style="padding:8px 12px;border:1px solid var(--border,#e0dbd4);border-radius:8px;font-size:.875rem;background:var(--bg,#fff);color:inherit;min-width:160px">
            <option value="">Semua Kelompok</option>
            <?php foreach ($kelompok ?? [] as $k): ?>
                <option value="<?= esc($k['nama_kelompok']) ?>"><?= esc($k['nama_kelompok']) ?></option>
            <?php endforeach; ?>
            <option value="-">Belum ada kelompok</option>
        </select>
        <div id="mhs-count-info" style="font-size:.8rem;color:var(--muted,#888);white-space:nowrap"></div>
    </div>
</div>

<!-- Table -->
<div class="card" style="overflow:hidden">
    <div class="table-wrap" style="overflow-x:auto">
        <table class="data" id="mhs-table" style="width:100%;min-width:780px">
            <thead>
                <tr>
                    <th style="width:44px"></th>
                    <th>Nama & Akun</th>
                    <th>NPM</th>
                    <th>Program Studi</th>
                    <th>Kelompok KKN</th>
                    <th>Kontak</th>
                    <th style="width:130px;text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($allMhs)): ?>
                <tr id="mhs-empty-default">
                    <td colspan="7" class="empty" style="text-align:center;padding:48px 16px">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2" style="opacity:.3;display:block;margin:0 auto 10px"><path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm13 10v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        Belum ada data mahasiswa
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
                        <td style="padding:12px 8px 12px 16px">
                            <div style="width:36px;height:36px;border-radius:50%;background:var(--accent-muted,#e8f0fe);color:var(--accent,#1B6B8A);font-weight:700;font-size:.9rem;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                <?= esc($initial) ?>
                            </div>
                        </td>
                        <td style="min-width:180px">
                            <div style="font-weight:600;font-size:.9rem"><?= esc($row['nama'] ?? '-') ?></div>
                            <div style="font-size:.78rem;color:var(--muted,#888);margin-top:2px"><?= esc($row['email'] ?? '-') ?></div>
                        </td>
                        <td>
                            <?php if ($npmTemp): ?>
                                <span style="display:inline-flex;align-items:center;gap:5px;font-size:.78rem;color:#b45309;background:#fef3c7;padding:3px 8px;border-radius:20px;font-weight:600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>
                                    Belum diisi
                                </span>
                            <?php else: ?>
                                <span class="font-mono" style="font-size:.875rem;letter-spacing:.03em"><?= esc($npm) ?></span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:.875rem"><?= esc($row['prodi'] ?? '-') ?></td>
                        <td>
                            <?php if ($hasKelompok): ?>
                                <span style="display:inline-flex;align-items:center;gap:5px;font-size:.78rem;color:#1e6b3a;background:#d1fae5;padding:3px 10px;border-radius:20px;font-weight:600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="12"/></svg>
                                    <?= esc($row['nama_kelompok']) ?>
                                </span>
                            <?php else: ?>
                                <span style="display:inline-flex;align-items:center;gap:5px;font-size:.78rem;color:#6b7280;background:#f3f4f6;padding:3px 10px;border-radius:20px">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="12"/></svg>
                                    Belum ditempatkan
                                </span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:.82rem;color:var(--muted,#888)">
                            <?php if (! empty($row['no_hp'])): ?>
                                <a href="https://wa.me/<?= preg_replace('/\D/', '', $row['no_hp']) ?>" target="_blank" rel="noopener"
                                   style="display:inline-flex;align-items:center;gap:5px;color:#16a34a;text-decoration:none;font-size:.8rem">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M20.52 3.45A11.82 11.82 0 0 0 12 0C5.37 0 0 5.37 0 12c0 2.11.55 4.16 1.6 5.97L0 24l6.2-1.57A11.94 11.94 0 0 0 12 24c6.63 0 12-5.37 12-12 0-3.2-1.25-6.22-3.48-8.55zM12 22c-1.85 0-3.66-.5-5.24-1.44l-.38-.22-3.88.98.99-3.76-.25-.4A9.96 9.96 0 0 1 2 12C2 6.48 6.48 2 12 2c2.67 0 5.18 1.04 7.07 2.93A9.93 9.93 0 0 1 22 12c0 5.52-4.48 10-10 10zm5.5-7.5c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.76.97-.93 1.17-.17.2-.35.22-.65.07-.3-.15-1.27-.47-2.42-1.5-.9-.8-1.5-1.78-1.67-2.08-.17-.3-.02-.46.13-.6.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.6-.92-2.2-.24-.57-.5-.5-.67-.5H7c-.2 0-.5.07-.77.37-.27.3-1 1-1 2.44s1.03 2.83 1.17 3.03c.15.2 2.02 3.1 4.9 4.35.68.3 1.22.47 1.63.6.69.22 1.31.19 1.81.12.55-.08 1.76-.72 2.01-1.42.25-.7.25-1.3.17-1.42-.07-.12-.27-.2-.57-.35z"/></svg>
                                    <?= esc($row['no_hp']) ?>
                                </a>
                            <?php else: ?>
                                <span style="color:#ccc">—</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:center">
                            <div style="display:inline-flex;gap:6px;align-items:center">
                                <a href="<?= site_url('admin/mahasiswa/' . $row['id'] . '/edit') ?>"
                                   title="Edit akun mahasiswa"
                                   style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;background:var(--bg2,#f5f3f0);color:var(--ink,#2C2825);text-decoration:none;transition:background .15s"
                                   onmouseover="this.style.background='#dbeafe';this.style.color='#1d4ed8'"
                                   onmouseout="this.style.background='var(--bg2,#f5f3f0)';this.style.color='var(--ink,#2C2825)'">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-5m-1.414-9.414a2 2 0 1 1 2.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form method="post" action="<?= site_url('admin/mahasiswa/' . $row['id'] . '/delete') ?>"
                                      data-confirm="Hapus akun mahasiswa <?= esc($row['nama'] ?? '', 'attr') ?>?">
                                    <?= csrf_field() ?>
                                    <button type="submit" title="Hapus mahasiswa"
                                        style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;background:var(--bg2,#f5f3f0);color:var(--ink,#2C2825);border:none;cursor:pointer;transition:background .15s"
                                        onmouseover="this.style.background='#fee2e2';this.style.color='#dc2626'"
                                        onmouseout="this.style.background='var(--bg2,#f5f3f0)';this.style.color='var(--ink,#2C2825)'">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr id="mhs-no-result" style="display:none">
                    <td colspan="7" class="empty" style="text-align:center;padding:36px 16px;color:var(--muted,#888)">
                        Tidak ada mahasiswa yang cocok dengan pencarian.
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
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        if (noResult) noResult.style.display = (visible === 0 && total > 0) ? '' : 'none';
        if (countInfo) {
            countInfo.textContent = (q || grp) ? `Menampilkan ${visible} dari ${total}` : '';
        }
    }

    searchEl?.addEventListener('input', applyFilter);
    filterEl?.addEventListener('change', applyFilter);
})();
</script>

<style>
[data-theme=dark] #mhs-search,
[data-theme=dark] #mhs-filter-kelompok {
    background: var(--surface, #1e1e1e);
    border-color: var(--border, #333);
    color: var(--ink, #e8e4de);
}
.mhs-row { transition: background .12s; }
.mhs-row:hover td { background: var(--bg2, #faf9f7); }
@media (max-width: 640px) {
    #mhs-table td:nth-child(4),
    #mhs-table th:nth-child(4) { display: none; }
}
</style>
