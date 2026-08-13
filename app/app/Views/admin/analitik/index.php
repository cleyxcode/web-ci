<div class="stat-row">
    <div class="stat">
        <div class="label">Akurasi KNN</div>
        <div class="value"><?= $knnAkurasi !== null ? esc($knnAkurasi) . '%' : '—' ?></div>
        <small><?= (int) $knnMatch ?> cocok dari <?= (int) $knnTotal ?> penilaian</small>
    </div>
    <div class="stat">
        <div class="label">Total penilaian</div>
        <div class="value"><?= count($nilai ?? []) ?></div>
    </div>
    <div class="stat">
        <div class="label">Kelompok ada GPS</div>
        <div class="value"><?= count($petaKelompok ?? []) ?></div>
    </div>
    <div class="stat">
        <div class="label">Kelompok</div>
        <div class="value"><?= count($kelengkapan ?? []) ?></div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px" class="dash-grid">
    <div class="card">
        <div class="card-head"><h2>Distribusi grade aktual</h2></div>
        <canvas id="chartGrade" height="180"></canvas>
    </div>
    <div class="card">
        <div class="card-head"><h2>Prediksi KNN vs aktual</h2></div>
        <canvas id="chartKnn" height="180"></canvas>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px" class="dash-grid">
    <div class="card">
        <div class="card-head"><h2>Status logbook</h2></div>
        <canvas id="chartLogStatus" height="180"></canvas>
    </div>
    <div class="card">
        <div class="card-head"><h2>Peta lokasi GPS kelompok</h2></div>
        <?= view('partials/map', [
            'mapId'   => 'map-analitik',
            'markers' => $petaKelompok ?? [],
            'zoom'    => 10,
            'class'   => 'map-box',
            'empty'   => 'Belum ada kelompok yang menetapkan lokasi GPS.',
        ]) ?>
    </div>
</div>

<div class="card" style="margin-top:16px">
    <div class="card-head"><h2>Kelengkapan per kelompok</h2></div>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>Kelompok</th>
                    <th>Anggota</th>
                    <th>Logbook</th>
                    <th>Tervalidasi</th>
                    <th>Laporan</th>
                    <th>GPS</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($kelengkapan)): ?>
                <tr><td colspan="6" class="empty">Belum ada data.</td></tr>
            <?php else: ?>
                <?php foreach ($kelengkapan as $row): ?>
                    <tr>
                        <td><?= esc($row['nama']) ?></td>
                        <td class="font-mono"><?= (int) $row['anggota'] ?></td>
                        <td class="font-mono"><?= (int) $row['logbook'] ?></td>
                        <td class="font-mono"><?= (int) $row['valid'] ?></td>
                        <td class="font-mono"><?= (int) $row['laporan'] ?></td>
                        <td><?= $row['ada_gps'] ? '✓' : '—' ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card" style="margin-top:16px">
    <div class="card-head"><h2>Perbandingan grade vs prediksi KNN</h2></div>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>NPM</th>
                    <th>Nama</th>
                    <th>Nilai akhir</th>
                    <th>Grade</th>
                    <th>Prediksi KNN</th>
                    <th>Cocok?</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($nilai)): ?>
                <tr><td colspan="6" class="empty">Belum ada penilaian. Prediksi KNN akan muncul setelah DPL menilai mahasiswa.</td></tr>
            <?php else: ?>
                <?php foreach ($nilai as $n): ?>
                    <?php $match = ! empty($n['prediksi_knn']) && $n['prediksi_knn'] === ($n['grade'] ?? ''); ?>
                    <tr>
                        <td class="font-mono"><?= esc($n['npm'] ?? '-') ?></td>
                        <td><?= esc($n['nama_mahasiswa'] ?? '-') ?></td>
                        <td class="font-mono"><?= esc(number_format((float) ($n['nilai_akhir'] ?? 0), 2)) ?></td>
                        <td class="<?= grade_class($n['grade'] ?? null) ?>"><?= esc($n['grade'] ?? '-') ?></td>
                        <td class="font-mono"><?= esc($n['prediksi_knn'] ?? '-') ?></td>
                        <td><?= empty($n['prediksi_knn']) ? '—' : ($match ? 'Ya' : 'Tidak') ?></td>
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
  const ink = isDark ? '#E8E4DE' : '#2C2825';
  const muted = isDark ? '#9A948C' : '#6B6560';
  const gridColor = isDark ? 'rgba(232,228,222,0.06)' : 'rgba(44,40,37,0.06)';
  const labels = ['A', 'B', 'BC', 'C', 'D'];
  const gradeRaw = <?= json_encode($gradeDist ?? []) ?>;
  const knnRaw = <?= json_encode($knnDist ?? []) ?>;
  const gradeValues = labels.map((g) => Number(gradeRaw[g] || 0));
  const knnValues = labels.map((g) => Number(knnRaw[g] || 0));
  const logLabels = <?= json_encode(array_column($logStatus ?? [], 'status')) ?>;
  const logValues = <?= json_encode(array_map('intval', array_column($logStatus ?? [], 'total'))) ?>;

  if (document.getElementById('chartGrade')) {
    new Chart(document.getElementById('chartGrade'), {
      type: 'doughnut',
      data: {
        labels: labels,
        datasets: [{ data: gradeValues.some((v) => v > 0) ? gradeValues : [1], backgroundColor: ['#2D7A4F','#1B6B8A','#C4920A','#B83232','#6B6560'], borderWidth: 0 }],
      },
      options: { plugins: { legend: { position: 'bottom', labels: { color: ink, boxWidth: 10 } } }, cutout: '58%' },
    });
  }

  if (document.getElementById('chartKnn')) {
    new Chart(document.getElementById('chartKnn'), {
      type: 'bar',
      data: {
        labels,
        datasets: [
          { label: 'Grade aktual', data: gradeValues, backgroundColor: '#1B6B8A', borderRadius: 4, maxBarThickness: 22 },
          { label: 'Prediksi KNN', data: knnValues, backgroundColor: '#C4920A', borderRadius: 4, maxBarThickness: 22 },
        ],
      },
      options: {
        plugins: { legend: { position: 'bottom', labels: { color: ink, boxWidth: 10, font: { size: 11 } } } },
        scales: {
          x: { ticks: { color: muted }, grid: { display: false } },
          y: { beginAtZero: true, ticks: { color: muted, precision: 0 }, grid: { color: gridColor } },
        },
      },
    });
  }

  if (document.getElementById('chartLogStatus')) {
    new Chart(document.getElementById('chartLogStatus'), {
      type: 'bar',
      data: {
        labels: logLabels.length ? logLabels : ['—'],
        datasets: [{ label: 'Logbook', data: logValues.length ? logValues : [0], backgroundColor: '#1B6B8A', borderRadius: 6, maxBarThickness: 36 }],
      },
      options: {
        plugins: { legend: { display: false } },
        scales: {
          x: { ticks: { color: ink }, grid: { display: false } },
          y: { beginAtZero: true, ticks: { color: ink, precision: 0 }, grid: { color: gridColor } },
        },
      },
    });
  }
});
</script>
<style>@media (max-width:900px){.dash-grid{grid-template-columns:1fr!important}}</style>
