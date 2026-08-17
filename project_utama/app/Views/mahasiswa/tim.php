<?php
$m = $mahasiswa ?? [];
$k = $kelompok ?? null;
$sayaId = (int) ($m['id'] ?? 0);
$isKetua = ! empty($isKetua);
$hasGps = $k && ! empty($k['latitude']) && ! empty($k['longitude']);
$ketuaId = (int) ($k['ketua_mahasiswa_id'] ?? 0);
?>

<?php if (! $k): ?>
    <div class="flex flex-col items-center justify-center py-20 px-6 text-center">
        <div class="mx-auto mb-6 grid h-24 w-24 place-items-center rounded-3xl bg-blue-50 text-blue-400 shadow-sm ring-1 ring-blue-100 dark:bg-blue-900/20 dark:ring-blue-900/50">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" class="h-12 w-12">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/>
            </svg>
        </div>
        <h3 class="text-lg font-extrabold text-slate-800 dark:text-white">Belum Ada Tim KKN</h3>
        <p class="mt-2 max-w-md text-sm text-slate-400 leading-relaxed">Anda belum ditempatkan di kelompok KKN. Silakan hubungi admin kampus agar dapat dimasukkan ke dalam tim KKN.</p>
        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 text-left shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-violet-100 text-violet-600 dark:bg-violet-900/50">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-slate-600 dark:text-slate-300">Hubungi admin kampus untuk penempatan kelompok</span>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="hero-strip">
        <div class="periode"><?= esc($k['periode'] ?? 'Periode KKN') ?></div>
        <h2><?= esc($k['nama_kelompok']) ?></h2>
        <p>
            <?= count($anggota ?? []) ?> anggota · <?= format_tanggal($k['tanggal_mulai'] ?? null) ?> – <?= format_tanggal($k['tanggal_selesai'] ?? null) ?>
            <?php if ($isKetua): ?> · <span class="badge-count">Anda ketua</span><?php endif; ?>
        </p>
    </div>

    <div class="info-grid">
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

    <div class="card">
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
            <p class="field-hint">
                Titik resmi kelompok: <span class="font-mono"><?= esc($k['latitude']) ?>, <?= esc($k['longitude']) ?></span>
            </p>
        <?php elseif (! $hasGps): ?>
            <div class="flex flex-col items-center justify-center py-10 px-6 text-center">
                <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-2xl bg-slate-100 text-slate-300 dark:bg-slate-800 dark:text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" class="h-8 w-8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                    </svg>
                </div>
                <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Belum Ada Lokasi</p>
                <p class="mt-1 text-xs text-slate-400">Lokasi GPS belum ditetapkan oleh ketua kelompok.</p>
            </div>
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
                    <tr>
                        <td colspan="4">
                            <div class="flex flex-col items-center justify-center py-14 px-6 text-center">
                                <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-2xl bg-slate-100 text-slate-300 dark:bg-slate-800 dark:text-slate-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" class="h-8 w-8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Belum ada anggota</p>
                                <p class="mt-1 text-xs text-slate-400">Belum ada anggota di kelompok ini.</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($anggota as $row): ?>
                        <?php $isSaya = (int) $row['id'] === $sayaId; ?>
                        <tr class="<?= $isSaya ? 'row-highlight' : '' ?>">
                            <td class="font-mono"><?= esc($row['npm']) ?></td>
                            <td>
                                <?= esc($row['nama']) ?>
                                <?php if ($isSaya): ?><span class="badge-count">Anda</span><?php endif; ?>
                                <?php if ($ketuaId === (int) $row['id']): ?><span class="badge-count">Ketua</span><?php endif; ?>
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
