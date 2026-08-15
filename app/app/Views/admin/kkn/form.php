<?php
$isEdit = ! empty($kelompok);
$availableMembers = $isEdit ? ($belumDitempatkan ?? []) : ($mahasiswa ?? []);
$selectedMemberIds = old('mahasiswa_ids', []);
$selectedMemberIds = is_array($selectedMemberIds) ? array_map('intval', $selectedMemberIds) : [];
$currentLatitude = old('latitude', $kelompok['latitude'] ?? '');
$currentLongitude = old('longitude', $kelompok['longitude'] ?? '');
?>
<div class="card" style="max-width:860px">
    <div class="card-head"><h2><?= $isEdit ? 'Edit' : 'Tambah' ?> kelompok KKN</h2></div>
    <p class="field-hint" style="margin:-8px 0 18px">Atur DPL, lokasi penelitian, titik peta, anggota, dan ketua dari halaman ini. Data tersimpan bersama agar alur kelompok tetap sinkron.</p>
    <form method="post" action="<?= $isEdit ? site_url('admin/kkn/' . $kelompok['id']) : site_url('admin/kkn') ?>">
        <?= csrf_field() ?>
        <div class="form-grid">
            <div class="field">
                <label>Nama kelompok</label>
                <input type="text" name="nama_kelompok" placeholder="Kelompok 1 - Desa Waai" value="<?= esc(old('nama_kelompok', $kelompok['nama_kelompok'] ?? '')) ?>" required>
            </div>
            <div class="field">
                <label>Periode</label>
                <input type="text" name="periode" placeholder="2026 Genap" value="<?= esc(old('periode', $kelompok['periode'] ?? '')) ?>">
            </div>
            <div class="field">
                <label>DPL (Dosen Pembimbing Lapangan)</label>
                <select name="dpl_id" required>
                    <option value="">— Pilih DPL —</option>
                    <?php foreach ($dpl ?? [] as $row): ?>
                        <option value="<?= (int) $row['id'] ?>" <?= (string) old('dpl_id', $kelompok['dpl_id'] ?? '') === (string) $row['id'] ? 'selected' : '' ?>>
                            <?= esc($row['nama']) ?><?= ! empty($row['nidn']) ? ' · ' . esc($row['nidn']) : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="field-hint">DPL adalah dosen di lapangan. Buat akunnya dulu di menu DPL, lalu pilih di sini.</div>
            </div>
            <div class="field">
                <label>Lokasi penempatan</label>
                <select name="lokasi_id" id="lokasi_id">
                    <option value="">— Pilih desa —</option>
                    <?php foreach ($lokasi ?? [] as $row): ?>
                        <option value="<?= (int) $row['id'] ?>" <?= (string) old('lokasi_id', $kelompok['lokasi_id'] ?? '') === (string) $row['id'] ? 'selected' : '' ?>>
                            <?= esc(format_alamat($row)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="field-hint">Pilih lokasi yang sudah tersedia atau buat lokasi baru di bawah.</div>
            </div>
            <div class="field field-full">
                <details class="inline-create">
                    <summary>Lokasi belum tersedia? Tambah lokasi baru</summary>
                    <div class="inline-create-body">
                        <div class="form-grid">
                            <div class="field">
                                <label for="new_lokasi_nama_desa">Nama desa</label>
                                <input id="new_lokasi_nama_desa" type="text" name="new_lokasi_nama_desa" value="<?= esc(old('new_lokasi_nama_desa')) ?>" placeholder="Contoh: Waai">
                            </div>
                            <div class="field">
                                <label for="new_lokasi_kecamatan">Kecamatan</label>
                                <input id="new_lokasi_kecamatan" type="text" name="new_lokasi_kecamatan" value="<?= esc(old('new_lokasi_kecamatan')) ?>" placeholder="Contoh: Salahutu">
                            </div>
                            <div class="field field-full">
                                <label for="new_lokasi_kabupaten">Kabupaten/Kota</label>
                                <input id="new_lokasi_kabupaten" type="text" name="new_lokasi_kabupaten" value="<?= esc(old('new_lokasi_kabupaten')) ?>" placeholder="Contoh: Maluku Tengah">
                            </div>
                        </div>
                        <div class="field-hint">Jika diisi, lokasi baru otomatis disimpan dan langsung dipakai oleh kelompok ini.</div>
                    </div>
                </details>
            </div>
            <?php if (! empty($mahasiswa)): ?>
            <div class="field field-full">
                <label>Ketua kelompok</label>
                <select name="ketua_mahasiswa_id">
                    <option value="">— Pilih ketua —</option>
                    <?php foreach ($mahasiswa as $row): ?>
                        <option value="<?= (int) $row['id'] ?>" <?= (string) old('ketua_mahasiswa_id', $kelompok['ketua_mahasiswa_id'] ?? '') === (string) $row['id'] ? 'selected' : '' ?>>
                            <?= esc($row['nama']) ?> (<?= esc($row['npm']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="field-hint">Ketua harus merupakan anggota kelompok. Hanya ketua yang dapat memperbarui GPS lapangan dari menu Tim KKN.</div>
            </div>
            <?php endif; ?>
            <div class="field">
                <label>Tanggal mulai</label>
                <input type="date" name="tanggal_mulai" value="<?= esc(old('tanggal_mulai', $kelompok['tanggal_mulai'] ?? '')) ?>">
            </div>
            <div class="field">
                <label>Tanggal selesai</label>
                <input type="date" name="tanggal_selesai" value="<?= esc(old('tanggal_selesai', $kelompok['tanggal_selesai'] ?? '')) ?>">
            </div>
            <div class="field field-full">
                <label>Alamat penelitian</label>
                <textarea name="alamat_penelitian" rows="3" placeholder="Alamat lengkap lokasi penelitian KKN"><?= esc(old('alamat_penelitian', $kelompok['alamat_penelitian'] ?? '')) ?></textarea>
            </div>
            <div class="field field-full">
                <label>Titik lokasi penelitian di peta</label>
                <div class="field-hint" style="margin:0 0 8px">Klik peta untuk memilih titik. Koordinat ini menjadi titik resmi kelompok dan tetap dapat diperbarui ketua dari menu Tim KKN.</div>
                <div id="admin-location-map" class="map-box map-box-lg" data-map-editor="1" data-lat="<?= esc($currentLatitude) ?>" data-lng="<?= esc($currentLongitude) ?>"></div>
                <div class="form-grid location-coordinates" style="margin-top:12px">
                    <div class="field">
                        <label for="admin-location-latitude">Latitude</label>
                        <input type="text" name="latitude" id="admin-location-latitude" class="font-mono" value="<?= esc($currentLatitude) ?>" placeholder="-3.6950000" inputmode="decimal">
                    </div>
                    <div class="field">
                        <label for="admin-location-longitude">Longitude</label>
                        <input type="text" name="longitude" id="admin-location-longitude" class="font-mono" value="<?= esc($currentLongitude) ?>" placeholder="128.1830000" inputmode="decimal">
                    </div>
                </div>
                <div class="map-editor-actions">
                    <button type="button" class="btn btn-secondary btn-sm" data-location-use>Gunakan lokasi saya</button>
                    <button type="button" class="btn btn-secondary btn-sm" data-location-clear>Hapus titik</button>
                    <span class="field-hint" data-location-status>Belum ada titik dipilih.</span>
                </div>
            </div>
            <div class="field field-full">
                <label><?= $isEdit ? 'Tambahkan anggota baru' : 'Anggota awal kelompok' ?></label>
                <?php if (! empty($availableMembers)): ?>
                    <div class="member-pick-list">
                        <?php foreach ($availableMembers as $mhs): ?>
                            <label class="member-pick">
                                <input type="checkbox" name="mahasiswa_ids[]" value="<?= (int) $mhs['id'] ?>" <?= in_array((int) $mhs['id'], $selectedMemberIds, true) ? 'checked' : '' ?>>
                                <span>
                                    <strong><?= esc($mhs['nama']) ?></strong>
                                    <small class="font-mono"><?= esc($mhs['npm']) ?> · <?= esc($mhs['prodi'] ?? '-') ?></small>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <div class="field-hint"><?= $isEdit ? 'Centang mahasiswa yang ingin dimasukkan ke kelompok ini.' : 'Centang anggota yang akan menjadi bagian kelompok ini.' ?></div>
                <?php elseif ($isEdit): ?>
                    <div class="empty">Semua mahasiswa sudah ditempatkan di kelompok lain.</div>
                <?php else: ?>
                    <div class="empty">Belum ada mahasiswa tanpa kelompok.</div>
                <?php endif; ?>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Simpan' : 'Tambah' ?></button>
            <a href="<?= site_url('admin/kkn') ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
