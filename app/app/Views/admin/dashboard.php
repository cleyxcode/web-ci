<div class="dashboard-page dashboard-admin admin-dashboard">
    <section class="admin-welcome">
        <div>
            <p>Ruang kendali KKN Tematik</p>
            <h2>Selamat datang kembali, <?= esc($user['nama'] ?? 'Admin') ?> <span aria-hidden="true">👋</span></h2>
            <span>Pantau mahasiswa, kelompok, dan aktivitas KKN dari satu tempat.</span>
        </div>
        <a href="<?= site_url('admin/mahasiswa/create') ?>" class="btn btn-primary admin-welcome-action">Tambah mahasiswa</a>
    </section>

    <!-- Dashboard Summary Bar -->
    <?php
    $totalAll = max(1, (int) ($totalMahasiswa ?? 0));
    $logbookValidated = (int) ($logbookValidated ?? 0);
    $logbookTotal = max(1, (int) ($totalLogbook ?? 0));
    $laporanDiterima = (int) ($laporanDiterima ?? 0);
    $laporanTotal = max(1, (int) ($totalLaporan ?? 0));
    $pctLogbook = round(($logbookValidated / $logbookTotal) * 100);
    $pctLaporan = round(($laporanDiterima / $laporanTotal) * 100);
    ?>
    <section class="mb-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4" aria-label="Dashboard bar statistik">
        <div class="flex items-center gap-4 rounded-2xl border border-violet-100 bg-white p-4 shadow-sm dark:border-violet-900/30 dark:bg-slate-900">
            <div class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-violet-100 text-violet-600 dark:bg-violet-900/50 dark:text-violet-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm13 10v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-bold text-slate-500">Total Mahasiswa</p>
                <strong class="js-count-up block text-2xl font-extrabold text-slate-900 dark:text-white" data-count-up="<?= (int) ($totalMahasiswa ?? 0) ?>"><?= (int) ($totalMahasiswa ?? 0) ?></strong>
                <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                    <div class="h-full rounded-full bg-violet-500 transition-all duration-1000" style="width: <?= $totalMahasiswa > 0 ? '100' : '0' ?>%"></div>
                </div>
                <small class="text-xs text-slate-400">Mahasiswa aktif KKN</small>
            </div>
        </div>

        <div class="flex items-center gap-4 rounded-2xl border border-blue-100 bg-white p-4 shadow-sm dark:border-blue-900/30 dark:bg-slate-900">
            <div class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 9a7 7 0 0 1 14 0"/></svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-bold text-slate-500">Dosen Pembimbing Lapangan</p>
                <strong class="js-count-up block text-2xl font-extrabold text-slate-900 dark:text-white" data-count-up="<?= (int) ($totalDpl ?? 0) ?>"><?= (int) ($totalDpl ?? 0) ?></strong>
                <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                    <div class="h-full rounded-full bg-blue-500 transition-all duration-1000" style="width: <?= $totalDpl > 0 ? '100' : '0' ?>%"></div>
                </div>
                <small class="text-xs text-slate-400">Dosen pembimbing aktif</small>
            </div>
        </div>

        <div class="flex items-center gap-4 rounded-2xl border border-emerald-100 bg-white p-4 shadow-sm dark:border-emerald-900/30 dark:bg-slate-900">
            <div class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-bold text-slate-500">Total Lokasi KKN</p>
                <strong class="js-count-up block text-2xl font-extrabold text-slate-900 dark:text-white" data-count-up="<?= (int) ($totalLokasi ?? 0) ?>"><?= (int) ($totalLokasi ?? 0) ?></strong>
                <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                    <div class="h-full rounded-full bg-emerald-500 transition-all duration-1000" style="width: <?= $totalLokasi > 0 ? '100' : '0' ?>%"></div>
                </div>
                <small class="text-xs text-slate-400">Lokasi penempatan</small>
            </div>
        </div>

        <div class="flex items-center gap-4 rounded-2xl border border-amber-100 bg-white p-4 shadow-sm dark:border-amber-900/30 dark:bg-slate-900">
            <div class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-900/50 dark:text-amber-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-bold text-slate-500">Logbook Divalidasi</p>
                <strong class="js-count-up block text-2xl font-extrabold text-slate-900 dark:text-white" data-count-up="<?= (int) ($logbookValidated ?? 0) ?>"><?= (int) ($logbookValidated ?? 0) ?></strong>
                <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                    <div class="h-full rounded-full bg-amber-500 transition-all duration-1000" style="width: <?= $pctLogbook ?>%"></div>
                </div>
                <small class="text-xs text-slate-400">dari <?= (int) ($totalLogbook ?? 0) ?> total (<?= $pctLogbook ?>%)</small>
            </div>
        </div>
    </section>

    <!-- Secondary stat row -->
    <section class="mb-5 grid grid-cols-2 gap-3 xl:grid-cols-4" aria-label="Ringkasan tambahan">
        <article class="admin-stat-card tone-indigo">
            <span class="admin-stat-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19V5m0 14h16M8 16v-5m4 5V7m4 9v-3"/></svg></span>
            <div><span>Kelompok KKN</span><strong class="js-count-up" data-count-up="<?= (int) ($totalKelompok ?? 0) ?>"><?= (int) ($totalKelompok ?? 0) ?></strong><small>Kelompok terkelola</small></div>
        </article>
        <article class="admin-stat-card tone-rose">
            <span class="admin-stat-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6Zm0 0v6h6M8 13h8M8 17h5"/></svg></span>
            <div><span>Laporan</span><strong class="js-count-up" data-count-up="<?= (int) ($totalLaporan ?? 0) ?>"><?= (int) ($totalLaporan ?? 0) ?></strong><small><?= (int) ($laporanDiterima ?? 0) ?> diterima</small></div>
        </article>
        <article class="admin-stat-card tone-emerald">
            <span class="admin-stat-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></span>
            <div><span>Logbook periode</span><strong class="js-count-up" data-count-up="<?= (int) ($filteredLogbook ?? 0) ?>"><?= (int) ($filteredLogbook ?? 0) ?></strong><small><?= (int) ($totalLogbook ?? 0) ?> kegiatan tercatat</small></div>
        </article>
        <article class="admin-stat-card tone-violet">
            <span class="admin-stat-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m12 2 3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z"/></svg></span>
            <div><span>Evaluasi</span><strong class="js-count-up" data-count-up="<?= (int) ($totalEvaluasi ?? 0) ?>"><?= (int) ($totalEvaluasi ?? 0) ?></strong><small><?= $avgEvaluasi !== null ? esc(number_format((float) $avgEvaluasi, 1)) . '/5 rata-rata' : 'Belum ada' ?></small></div>
        </article>
    </section>

<?= view('partials/logbook-filter', [
    'filterRoute'  => site_url('admin/dashboard'),
    'filterPeriod' => $filterPeriod ?? 'minggu',
    'filterDate'   => $filterDate ?? date('Y-m-d'),
    'filterLabel'  => $filterLabel ?? 'Pilih rentang waktu',
]) ?>

<div class="card dashboard-panel chart-panel">
    <div class="card-head">
        <div>
            <h2>Statistik data terkelola</h2>
            <p class="mt-1 text-xs font-semibold text-slate-400">Perbandingan jumlah data utama dalam sistem KKN</p>
        </div>
    </div>
    <div class="relative h-64 sm:h-72">
        <canvas id="chartStatistikBar" aria-label="Grafik batang statistik data terkelola" role="img"></canvas>
    </div>
</div>

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
  const statistikLabels = <?= json_encode(array_column($statistikBar ?? [], 'label')) ?>;
  const statistikTotals = <?= json_encode(array_map('intval', array_column($statistikBar ?? [], 'total'))) ?>;
  const ink = isDark ? '#E8E4DE' : '#2C2825';
  const muted = isDark ? '#9A948C' : '#9A948C';
  const gridColor = isDark ? 'rgba(232,228,222,0.06)' : 'rgba(44,40,37,0.06)';

  if (document.getElementById('chartStatistikBar')) {
    new Chart(document.getElementById('chartStatistikBar'), {
      type: 'bar',
      data: {
        labels: statistikLabels.length ? statistikLabels : ['Belum ada data'],
        datasets: [{
          label: 'Jumlah data',
          data: statistikTotals.length ? statistikTotals : [0],
          backgroundColor: ['#6D4AFF', '#3977D8', '#1B6B8A', '#2D7A4F', '#C4920A', '#C45475', '#8E5AC8'],
          borderRadius: 8,
          borderSkipped: false,
          maxBarThickness: 42,
        }],
      },
      options: {
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: { callbacks: { label: (context) => ` ${context.parsed.y.toLocaleString('id-ID')} data` } },
        },
        scales: {
          x: { ticks: { color: muted, font: { size: 11, weight: '600' } }, grid: { display: false } },
          y: { beginAtZero: true, ticks: { color: muted, precision: 0 }, grid: { color: gridColor } },
        },
      },
    });
  }

  if (document.getElementById('chartLogbook')) {
    new Chart(document.getElementById('chartLogbook'), {
      type: 'line',
      data: {
        labels: logbookLabels.length ? logbookLabels.map((value) => value.length === 10 ? value.slice(8, 10) + '/' + value.slice(5, 7) : value) : ['—'],
        datasets: [{
          label: 'Logbook',
          data: logbookTotals.length ? logbookTotals : [0],
          backgroundColor: 'rgba(27,107,138,.14)',
          borderColor: '#1B6B8A',
          borderWidth: 3,
          pointBackgroundColor: '#fff',
          pointBorderColor: '#1B6B8A',
          pointBorderWidth: 2,
          pointRadius: 4,
          pointHoverRadius: 7,
          tension: .38,
          fill: true,
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
