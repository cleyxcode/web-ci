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
            <span class="info-label">DPL</span>
            <strong><?= esc($k['nama_dpl'] ?? 'Belum ditentukan') ?></strong>
            <?php if (! empty($k['no_hp_dpl'])): ?>
                <small><?= esc($k['no_hp_dpl']) ?></small>
            <?php endif; ?>
        </div>
        <div class="info-card">
            <span class="info-label">Dosen pendamping</span>
            <strong><?= trim($k['dosen_pendamping'] ?? '') !== '' ? esc($k['dosen_pendamping']) : 'Belum diisi' ?></strong>
            <?php if (! empty($k['no_hp_dosen_pendamping'])): ?>
                <small><?= esc($k['no_hp_dosen_pendamping']) ?></small>
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

        <?php if ($hasGps): ?>
            <?= view('partials/map', [
                'mapId'   => 'map-tim',
                'markers' => [$k],
                'zoom'    => 15,
                'class'   => 'map-box',
            ]) ?>
            <p class="field-hint" style="margin-top:8px">
                Koordinat: <span class="font-mono"><?= esc($k['latitude']) ?>, <?= esc($k['longitude']) ?></span>
            </p>
        <?php else: ?>
            <p class="empty" style="padding:12px 0">Lokasi GPS belum ditetapkan oleh ketua kelompok.</p>
        <?php endif; ?>

        <?php if ($isKetua): ?>
            <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border-lembut)">
                <p style="margin:0 0 12px;font-size:0.9rem;color:var(--abu-karang)">
                    Sebagai ketua, Anda dapat mengambil lokasi perangkat saat ini atau mengisi koordinat manual.
                </p>
                <form method="post" action="<?= site_url('mahasiswa/tim/gps') ?>" id="form-gps">
                    <?= csrf_field() ?>
                    <div class="form-grid">
                        <div class="field">
                            <label>Latitude</label>
                            <input type="text" name="latitude" id="gps-lat" class="font-mono" required
                                   value="<?= esc($k['latitude'] ?? '') ?>" placeholder="-3.6950000">
                        </div>
                        <div class="field">
                            <label>Longitude</label>
                            <input type="text" name="longitude" id="gps-lng" class="font-mono" required
                                   value="<?= esc($k['longitude'] ?? '') ?>" placeholder="128.1830000">
                        </div>
                    </div>
                    <div class="form-actions" style="border:none;padding-top:0">
                        <button type="button" class="btn btn-secondary" id="btn-ambil-gps">Ambil lokasi perangkat</button>
                        <button type="submit" class="btn btn-primary">Simpan lokasi GPS</button>
                    </div>
                    <p id="gps-status" class="field-hint"></p>
                </form>
            </div>
        <?php elseif (! $hasGps): ?>
            <p class="field-hint">Hubungi ketua kelompok agar lokasi GPS lapangan ditetapkan.</p>
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

    <script>
    document.addEventListener('DOMContentLoaded', () => {
      const btn = document.getElementById('btn-ambil-gps');
      const status = document.getElementById('gps-status');
      if (btn) {
        btn.addEventListener('click', () => {
          if (!navigator.geolocation) {
            status.textContent = 'Browser tidak mendukung geolocation.';
            return;
          }
          status.textContent = 'Mengambil lokasi…';
          navigator.geolocation.getCurrentPosition(
            (pos) => {
              document.getElementById('gps-lat').value = pos.coords.latitude.toFixed(7);
              document.getElementById('gps-lng').value = pos.coords.longitude.toFixed(7);
              status.textContent = 'Lokasi perangkat berhasil diambil. Klik Simpan untuk menyimpan.';
            },
            () => { status.textContent = 'Gagal mengambil lokasi. Izinkan akses lokasi di browser.'; },
            { enableHighAccuracy: true, timeout: 15000 }
          );
        });
      }
    });
    </script>
<?php endif; ?>
