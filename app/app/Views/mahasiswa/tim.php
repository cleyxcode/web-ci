<?php
$m = $mahasiswa ?? [];
$k = $kelompok ?? null;
$sayaId = (int) ($m['id'] ?? 0);
$isKetua = ! empty($isKetua);
$hasGps = $k && ! empty($k['latitude']) && ! empty($k['longitude']);
$ketuaId = (int) ($k['ketua_mahasiswa_id'] ?? 0);
?>

<?php if (! $k): ?>
    <div class="card">
        <p class="empty">Anda belum ditempatkan di kelompok KKN. Hubungi admin agar dimasukkan ke tim.</p>
    </div>
<?php else: ?>
    <div class="hero-strip">
        <div class="periode"><?= esc($k['periode'] ?? 'Periode KKN') ?></div>
        <h2 style="margin:0 0 4px;font-size:1.25rem"><?= esc($k['nama_kelompok']) ?></h2>
        <p style="margin:0;color:var(--abu-karang);font-size:0.9rem">
            <?= count($anggota ?? []) ?> anggota · <?= format_tanggal($k['tanggal_mulai'] ?? null) ?> – <?= format_tanggal($k['tanggal_selesai'] ?? null) ?>
            <?php if ($isKetua): ?> · <span class="badge-count">Anda ketua</span><?php endif; ?>
        </p>
    </div>

    <div class="info-grid" style="margin-bottom:16px">
        <div class="info-card">
            <span class="info-label">DPL (Dosen Pembimbing Lapangan)</span>
            <strong><?= esc($k['nama_dpl'] ?? 'Belum ditentukan') ?></strong>
            <?php if (! empty($k['no_hp_dpl'])): ?>
                <small><?= esc($k['no_hp_dpl']) ?></small>
            <?php endif; ?>
        </div>
        <div class="info-card">
            <span class="info-label">Ketua kelompok</span>
            <strong><?= esc($k['nama_ketua'] ?? 'Belum ditunjuk') ?></strong>
        </div>
        <div class="info-card">
            <span class="info-label">Lokasi penempatan</span>
            <strong><?= esc(format_alamat($k)) ?></strong>
        </div>
        <div class="info-card">
            <span class="info-label">Alamat penelitian</span>
            <strong><?= trim($k['alamat_penelitian'] ?? '') !== '' ? esc($k['alamat_penelitian']) : 'Belum diisi' ?></strong>
        </div>
    </div>

    <div class="card" style="margin-bottom:16px">
        <div class="card-head">
            <h2>Lokasi GPS lapangan</h2>
            <?php if ($hasGps): ?>
                <small class="field-hint">Ditetapkan <?= format_tanggal($k['lokasi_gps_at'] ?? null) ?></small>
            <?php endif; ?>
        </div>

        <?php if ($isKetua): ?>
            <form method="post" action="<?= site_url('mahasiswa/tim/gps') ?>" id="form-gps" class="location-picker-form">
                    <?= csrf_field() ?>
                    <p class="location-picker-note">Klik titik lokasi penelitian di peta, atau gunakan lokasi perangkat saat berada di lapangan.</p>
                    <div class="location-picker">
                        <div id="student-location-map" class="map-box map-box-lg" data-map-editor="1"
                             data-lat="<?= esc($k['latitude'] ?? '') ?>" data-lng="<?= esc($k['longitude'] ?? '') ?>"></div>
                        <input type="hidden" name="latitude" data-location-latitude value="<?= esc($k['latitude'] ?? '') ?>" required>
                        <input type="hidden" name="longitude" data-location-longitude value="<?= esc($k['longitude'] ?? '') ?>" required>
                    </div>
                    <div class="map-editor-actions">
                        <button type="button" class="btn btn-secondary" data-location-use>Gunakan lokasi saya</button>
                        <button type="button" class="btn btn-secondary" data-location-clear>Hapus titik</button>
                        <button type="submit" class="btn btn-primary">Simpan titik lokasi</button>
                        <span class="field-hint" data-location-status><?= $hasGps ? 'Titik tersimpan. Anda dapat memindahkannya di peta.' : 'Belum ada titik dipilih.' ?></span>
                    </div>
            </form>
        <?php elseif ($hasGps): ?>
            <?= view('partials/map', [
                'mapId'   => 'map-tim',
                'markers' => [$k],
                'zoom'    => 15,
                'class'   => 'map-box',
            ]) ?>
            <p class="field-hint" style="margin-top:8px">
                Titik resmi kelompok: <span class="font-mono"><?= esc($k['latitude']) ?>, <?= esc($k['longitude']) ?></span>
            </p>
        <?php elseif (! $hasGps): ?>
            <p class="empty" style="padding:12px 0">Lokasi GPS belum ditetapkan oleh ketua kelompok.</p>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-head">
            <h2>Anggota tim</h2>
            <span class="badge-count"><?= count($anggota ?? []) ?> orang</span>
        </div>
        <div class="table-wrap">
            <table class="data">
                <thead>
                    <tr>
                        <th>NPM</th>
                        <th>Nama</th>
                        <th>Prodi</th>
                        <th>Kontak</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($anggota)): ?>
                    <tr><td colspan="4" class="empty">Belum ada anggota di kelompok ini.</td></tr>
                <?php else: ?>
                    <?php foreach ($anggota as $row): ?>
                        <?php $isSaya = (int) $row['id'] === $sayaId; ?>
                        <tr class="<?= $isSaya ? 'row-highlight' : '' ?>">
                            <td class="font-mono"><?= esc($row['npm']) ?></td>
                            <td>
                                <?= esc($row['nama']) ?>
                                <?php if ($isSaya): ?><span class="badge-count" style="margin-left:6px">Anda</span><?php endif; ?>
                                <?php if ($ketuaId === (int) $row['id']): ?><span class="badge-count" style="margin-left:6px">Ketua</span><?php endif; ?>
                            </td>
                            <td><?= esc($row['prodi'] ?? '-') ?></td>
                            <td>
                                <?php if (! empty($row['no_hp'])): ?>
                                    <?= esc($row['no_hp']) ?>
                                <?php elseif (! empty($row['email'])): ?>
                                    <?= esc($row['email']) ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php endif; ?>
