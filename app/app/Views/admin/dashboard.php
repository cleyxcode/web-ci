<div class="dashboard-page dashboard-admin admin-dashboard">
    <section class="admin-welcome">
        <div>
            <p>Ruang kendali KKN Tematik</p>
            <h2>Selamat datang kembali, <?= esc($user['nama'] ?? 'Admin') ?> <span aria-hidden="true">👋</span></h2>
            <span>Pantau mahasiswa, kelompok, dan aktivitas KKN dari satu tempat.</span>
        </div>
        <a href="<?= site_url('admin/mahasiswa/create') ?>" class="btn btn-primary admin-welcome-action">Tambah mahasiswa</a>
    </section>

    <section class="admin-stat-grid" aria-label="Ringkasan KKN">
        <article class="admin-stat-card tone-violet">
            <span class="admin-stat-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm13 10v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
            <div><span>Total Mahasiswa</span><strong class="js-count-up" data-count-up="<?= (int) ($totalMahasiswa ?? 0) ?>"><?= (int) ($totalMahasiswa ?? 0) ?></strong><small>Mahasiswa aktif KKN</small></div>
        </article>
        <article class="admin-stat-card tone-blue">
            <span class="admin-stat-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 9a7 7 0 0 1 14 0M17 8a3 3 0 1 1 0-6m2 18a5 5 0 0 0-3.1-4.6"/></svg></span>
            <div><span>Total DPL</span><strong class="js-count-up" data-count-up="<?= (int) ($totalDpl ?? 0) ?>"><?= (int) ($totalDpl ?? 0) ?></strong><small>Dosen pembimbing</small></div>
        </article>
        <article class="admin-stat-card tone-emerald">
            <span class="admin-stat-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg></span>
            <div><span>Total Lokasi KKN</span><strong class="js-count-up" data-count-up="<?= (int) ($totalLokasi ?? 0) ?>"><?= (int) ($totalLokasi ?? 0) ?></strong><small>Lokasi penempatan</small></div>
        </article>
        <article class="admin-stat-card tone-amber">
            <span class="admin-stat-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="4" width="14" height="16" rx="2"/><path stroke-linecap="round" d="M8 2v4m8-4v4M8 11h8m-8 4h5"/></svg></span>
            <div><span>Logbook periode</span><strong class="js-count-up" data-count-up="<?= (int) ($filteredLogbook ?? 0) ?>"><?= (int) ($filteredLogbook ?? 0) ?></strong><small><?= (int) ($totalLogbook ?? 0) ?> kegiatan tercatat</small></div>
        </article>
        <article class="admin-stat-card tone-indigo">
            <span class="admin-stat-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19V5m0 14h16M8 16v-5m4 5V7m4 9v-3"/></svg></span>
            <div><span>Kelompok KKN</span><strong class="js-count-up" data-count-up="<?= (int) ($totalKelompok ?? 0) ?>"><?= (int) ($totalKelompok ?? 0) ?></strong><small>Kelompok terkelola</small></div>
        </article>
        <article class="admin-stat-card tone-rose">
            <span class="admin-stat-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6Zm0 0v6h6M8 13h8M8 17h5"/></svg></span>
            <div><span>Laporan</span><strong class="js-count-up" data-count-up="<?= (int) ($totalLaporan ?? 0) ?>"><?= (int) ($totalLaporan ?? 0) ?></strong><small>Laporan terkirim</small></div>
        </article>
    </section>

<?= view('partials/logbook-filter', [
    'filterRoute'  => site_url('admin/dashboard'),
    'filterPeriod' => $filterPeriod ?? 'minggu',
    'filterDate'   => $filterDate ?? date('Y-m-d'),
    'filterLabel'  => $filterLabel ?? 'Pilih rentang waktu',
]) ?>

<div class="dashboard-grid dashboard-grid-wide">
    <div class="card dashboard-panel chart-panel">
        <div class="card-head"><h2>Logbook · <?= esc($filterLabel ?? 'Periode terpilih') ?></h2></div>
        <canvas id="chartLogbook" height="160"></canvas>
    </div>
    <div class="card dashboard-panel chart-panel">
        <div class="card-head"><h2>Status laporan</h2></div>
        <canvas id="chartLaporan" height="160"></canvas>
    </div>
</div>

<div class="card dashboard-panel dashboard-map-panel">
    <div class="card-head">
        <h2>Peta lokasi GPS kelompok</h2>
        <a href="<?= site_url('admin/kkn') ?>" class="btn btn-secondary btn-sm">Kelompok KKN</a>
    </div>
    <?= view('partials/map', [
        'mapId'    => 'map-admin-dash',
        'markers'  => $petaKelompok ?? [],
        'zoom'     => 10,
        'class'    => 'map-box map-box-lg',
        'empty'    => 'Belum ada kelompok yang menetapkan lokasi GPS. Ketua kelompok mengirim koordinat dari menu Tim KKN.',
    ]) ?>
</div>

<div class="card dashboard-panel dashboard-table-panel">
    <div class="card-head">
        <h2>Mahasiswa terbaru</h2>
        <div>
            <a href="<?= site_url('admin/kkn') ?>" class="btn btn-secondary btn-sm">Kelola kelompok</a>
            <a href="<?= site_url('admin/mahasiswa') ?>" class="btn btn-secondary btn-sm">Lihat semua</a>
        </div>
    </div>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>NPM</th>
                    <th>Nama</th>
                    <th>Kelompok</th>
                    <th>Lokasi</th>
                    <th>Prodi</th>
                </tr>
            </thead>
            <tbody>
            <?php $rows = array_slice($mahasiswaTerbaru ?? [], 0, 8); ?>
            <?php if ($rows === []): ?>
                <tr>
                    <td colspan="5">
                        <div class="flex flex-col items-center justify-center py-12 px-6 text-center">
                            <div class="mx-auto mb-4 grid h-14 w-14 place-items-center rounded-2xl bg-slate-100 text-slate-300 dark:bg-slate-800 dark:text-slate-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" class="h-7 w-7">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Belum ada mahasiswa</p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td class="font-mono"><?= esc($row['npm'] ?? '-') ?></td>
                        <td><?= esc($row['nama'] ?? '-') ?></td>
                        <td><?= esc($row['nama_kelompok'] ?? '-') ?></td>
                        <td><?= esc($row['nama_desa'] ?? '-') ?></td>
                        <td><?= esc($row['prodi'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
  const logbookLabels = <?= json_encode(array_column($logbookPerMinggu ?? [], 'label')) ?>;
  const logbookTotals = <?= json_encode(array_map('intval', array_column($logbookPerMinggu ?? [], 'total'))) ?>;
  const statusLabels = <?= json_encode(array_column($laporanStatus ?? [], 'status')) ?>;
  const statusTotals = <?= json_encode(array_map('intval', array_column($laporanStatus ?? [], 'total'))) ?>;
  const ink = isDark ? '#E8E4DE' : '#2C2825';
  const muted = isDark ? '#9A948C' : '#9A948C';
  const gridColor = isDark ? 'rgba(232,228,222,0.06)' : 'rgba(44,40,37,0.06)';

  if (document.getElementById('chartLogbook')) {
    new Chart(document.getElementById('chartLogbook'), {
      type: 'bar',
      data: {
        labels: logbookLabels.length ? logbookLabels.map((value) => value.length === 10 ? value.slice(8, 10) + '/' + value.slice(5, 7) : value) : ['—'],
        datasets: [{
          label: 'Logbook',
          data: logbookTotals.length ? logbookTotals : [0],
          backgroundColor: '#1B6B8A',
          borderRadius: 6,
          maxBarThickness: 28,
        }],
      },
      options: {
        plugins: { legend: { display: false } },
        scales: {
          x: { ticks: { color: muted, font: { size: 10 } }, grid: { display: false } },
          y: { beginAtZero: true, ticks: { color: muted, precision: 0 }, grid: { color: gridColor } },
        },
      },
    });
  }

  if (document.getElementById('chartLaporan')) {
    new Chart(document.getElementById('chartLaporan'), {
      type: 'doughnut',
      data: {
        labels: statusLabels.length ? statusLabels : ['kosong'],
        datasets: [{
          data: statusTotals.length ? statusTotals : [1],
          backgroundColor: ['#C4920A', '#2D7A4F', '#B83232', '#1B6B8A'],
          borderWidth: 0,
        }],
      },
      options: {
        plugins: {
          legend: { position: 'bottom', labels: { color: ink, boxWidth: 10, font: { size: 11 } } },
        },
        cutout: '62%',
      },
    });
  }
});
</script>
</div>
