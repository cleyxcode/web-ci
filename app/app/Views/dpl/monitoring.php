<div class="card" style="margin-bottom:16px">
    <div class="card-head"><h2>Peta lokasi kelompok bimbingan</h2></div>
    <?= view('partials/map', [
        'mapId'   => 'map-dpl-monitor',
        'markers' => $petaKelompok ?? [],
        'zoom'    => 11,
        'class'   => 'map-box',
        'empty'   => 'Belum ada kelompok yang menetapkan lokasi GPS.',
    ]) ?>
</div>

<div class="card">
    <div class="card-head"><h2>Monitoring kegiatan mahasiswa</h2></div>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Mahasiswa</th>
                    <th>Kegiatan</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($logbooks)): ?>
                <tr><td colspan="5" class="empty">Belum ada kegiatan.</td></tr>
            <?php else: ?>
                <?php foreach ($logbooks as $row): ?>
                    <tr>
                        <td><?= format_tanggal($row['tanggal'] ?? null) ?></td>
                        <td><?= esc($row['nama_mahasiswa'] ?? $row['nama'] ?? '-') ?></td>
                        <td><?= esc($row['kegiatan']) ?></td>
                        <td><?= esc($row['lokasi_kegiatan'] ?? '-') ?></td>
                        <td><span class="<?= stempel_class($row['status']) ?>"><?= stempel_label($row['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
