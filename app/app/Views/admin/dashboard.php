<div class="stat-row">
    <div class="stat">
        <div class="label">Mahasiswa</div>
        <div class="value"><?= (int) ($totalMahasiswa ?? 0) ?></div>
    </div>
    <div class="stat">
        <div class="label">Kelompok KKN</div>
        <div class="value"><?= (int) ($totalKelompok ?? 0) ?></div>
    </div>
    <div class="stat">
        <div class="label">DPL</div>
        <div class="value"><?= (int) ($totalDpl ?? 0) ?></div>
    </div>
    <div class="stat">
        <div class="label">Lokasi</div>
        <div class="value"><?= (int) ($totalLokasi ?? 0) ?></div>
    </div>
    <div class="stat">
        <div class="label">Logbook</div>
        <div class="value"><?= (int) ($totalLogbook ?? 0) ?></div>
    </div>
    <div class="stat">
        <div class="label">Laporan</div>
        <div class="value"><?= (int) ($totalLaporan ?? 0) ?></div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1.2fr 1fr;gap:16px" class="dash-grid">
    <div class="card">
        <div class="card-head"><h2>Logbook per minggu</h2></div>
        <canvas id="chartLogbook" height="160"></canvas>
    </div>
    <div class="card">
        <div class="card-head"><h2>Status laporan</h2></div>
        <canvas id="chartLaporan" height="160"></canvas>
    </div>
</div>

<div class="card" style="margin-top:16px">
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

<div class="card" style="margin-top:16px">
    <div class="card-head">
        <h2>Mahasiswa terbaru</h2>
        <div style="display:flex;gap:8px">
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
                <tr><td colspan="5" class="empty">Belum ada data mahasiswa.</td></tr>
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
  const minggu = <?= json_encode(array_column($logbookPerMinggu ?? [], 'minggu')) ?>;
  const totalMinggu = <?= json_encode(array_map('intval', array_column($logbookPerMinggu ?? [], 'total'))) ?>;
  const statusLabels = <?= json_encode(array_column($laporanStatus ?? [], 'status')) ?>;
  const statusTotals = <?= json_encode(array_map('intval', array_column($laporanStatus ?? [], 'total'))) ?>;
  const ink = isDark ? '#E8E4DE' : '#2C2825';
  const muted = isDark ? '#9A948C' : '#9A948C';
  const gridColor = isDark ? 'rgba(232,228,222,0.06)' : 'rgba(44,40,37,0.06)';

  if (document.getElementById('chartLogbook')) {
    new Chart(document.getElementById('chartLogbook'), {
      type: 'bar',
      data: {
        labels: minggu.length ? minggu : ['—'],
        datasets: [{
          label: 'Logbook',
          data: totalMinggu.length ? totalMinggu : [0],
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
<style>
@media (max-width: 900px) {
  .dash-grid { grid-template-columns: 1fr !important; }
}
</style>
