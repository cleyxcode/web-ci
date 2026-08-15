<?php
/** @var array $mahasiswa  — data dari tabel mahasiswa (npm, prodi, no_hp, dll) */
/** @var array $profil     — data dari tabel users (nama, email, username) */
$npm = $mahasiswa['npm'] ?? '';
$npmKosong = empty($npm) || str_starts_with($npm, 'TEMP_');
$hasKelompok = ! empty($mahasiswa['kelompok_id']);
$hasGps = ! empty($mahasiswa['latitude']) && ! empty($mahasiswa['longitude']);
$isKetua = ! empty($isKetua);
?>

<?php if ($npmKosong): ?>
<div class="alert alert-warning" style="margin-bottom:16px;display:flex;align-items:flex-start;gap:10px">
    <svg xmlns="http://www.w3.org/2000/svg" style="flex-shrink:0;margin-top:2px" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
    </svg>
    <div>
        <strong>NPM belum diisi.</strong>
        Data studi Anda (NPM, program studi, nomor HP) belum lengkap.
        Lengkapi sekarang agar bisa dimasukkan ke kelompok KKN oleh admin.
    </div>
</div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px" class="dash-grid">

    <!-- Kartu 1: Info akun (nama, email) -->
    <div class="card">
        <div class="card-head"><h2>Akun</h2></div>
        <form method="post" action="<?= site_url('mahasiswa/profil') ?>">
            <?= csrf_field() ?>
            <div class="field">
                <label for="p_nama">Nama lengkap</label>
                <input type="text" id="p_nama" name="nama"
                       value="<?= esc(old('nama', $profil['nama'] ?? '')) ?>"
                       required minlength="3" autocomplete="name">
            </div>
            <div class="field">
                <label for="p_email">Email</label>
                <input type="email" id="p_email" name="email"
                       value="<?= esc(old('email', $profil['email'] ?? '')) ?>"
                       required autocomplete="email">
            </div>
            <div class="field">
                <label>Username</label>
                <input type="text" value="<?= esc($profil['username'] ?? '') ?>" disabled>
                <div class="field-hint">Username tidak dapat diubah</div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan akun</button>
            </div>
        </form>
    </div>

    <!-- Kartu 2: Data studi (NPM, prodi, no_hp) -->
    <div class="card" id="data-studi">
        <div class="card-head">
            <h2>Data Studi</h2>
            <?php if ($npmKosong): ?>
                <span class="stempel stempel-menunggu" style="font-size:0.72rem">Belum lengkap</span>
            <?php else: ?>
                <span class="stempel stempel-diterima" style="font-size:0.72rem">Lengkap</span>
            <?php endif; ?>
        </div>
        <form method="post" action="<?= site_url('mahasiswa/profil/data') ?>">
            <?= csrf_field() ?>
            <div class="field">
                <label for="p_npm">NPM <span style="color:#C0392B">*</span></label>
                <input type="text" id="p_npm" name="npm"
                       value="<?= $npmKosong ? '' : esc(old('npm', $npm)) ?>"
                       required placeholder="Nomor Pokok Mahasiswa"
                       maxlength="20" autocomplete="off">
            </div>
            <div class="field">
                <label for="p_prodi">Program Studi</label>
                <input type="text" id="p_prodi" name="prodi"
                       value="<?= esc(old('prodi', $mahasiswa['prodi'] ?? '')) ?>"
                       placeholder="Misal: Teknik Informatika"
                       maxlength="100">
            </div>
            <div class="field">
                <label for="p_no_hp">Nomor HP / WA</label>
                <input type="tel" id="p_no_hp" name="no_hp"
                       value="<?= esc(old('no_hp', $mahasiswa['no_hp'] ?? '')) ?>"
                       placeholder="08xxxxxxxxxx"
                       maxlength="20" autocomplete="tel">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan data studi</button>
            </div>
        </form>
    </div>

    <!-- Kartu 3: Lokasi KKN berbasis peta -->
    <div class="card profile-location-card" style="grid-column:1/-1">
        <div class="card-head">
            <div>
                <h2>Lokasi KKN</h2>
                <p class="field-hint" style="margin:4px 0 0">Titik lokasi dipilih langsung lewat peta, bukan diketik sebagai data alokasi.</p>
            </div>
            <a href="<?= site_url('mahasiswa/tim') ?>" class="btn btn-secondary btn-sm">Buka tim KKN</a>
        </div>

        <?php if (! $hasKelompok): ?>
            <p class="empty" style="padding:24px 0">Anda belum ditempatkan di kelompok KKN.</p>
        <?php elseif ($isKetua): ?>
            <form method="post" action="<?= site_url('mahasiswa/tim/gps') ?>" class="location-picker-form">
                <?= csrf_field() ?>
                <p class="location-picker-note">Sebagai ketua kelompok, klik peta untuk memindahkan titik lokasi penelitian.</p>
                <div class="location-picker">
                    <div id="student-profile-location-map" class="map-box map-box-lg" data-map-editor="1"
                         data-lat="<?= esc($mahasiswa['latitude'] ?? '') ?>" data-lng="<?= esc($mahasiswa['longitude'] ?? '') ?>"></div>
                    <input type="hidden" name="latitude" data-location-latitude value="<?= esc($mahasiswa['latitude'] ?? '') ?>" required>
                    <input type="hidden" name="longitude" data-location-longitude value="<?= esc($mahasiswa['longitude'] ?? '') ?>" required>
                </div>
                <div class="map-editor-actions">
                    <button type="button" class="btn btn-secondary btn-sm" data-location-use>Gunakan lokasi saya</button>
                    <button type="button" class="btn btn-secondary btn-sm" data-location-clear>Hapus titik</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan titik lokasi</button>
                    <span class="field-hint" data-location-status><?= $hasGps ? 'Titik tersimpan dan dapat diperbarui.' : 'Belum ada titik dipilih.' ?></span>
                </div>
            </form>
        <?php elseif ($hasGps): ?>
            <?= view('partials/map', [
                'mapId'   => 'map-profil-mhs',
                'markers' => [$mahasiswa],
                'zoom'    => 15,
                'class'   => 'map-box',
            ]) ?>
            <p class="field-hint" style="margin-top:8px">Titik lokasi ditetapkan oleh ketua kelompok.</p>
        <?php else: ?>
            <p class="empty" style="padding:24px 0">Lokasi GPS belum ditetapkan oleh ketua kelompok.</p>
        <?php endif; ?>
    </div>

    <!-- Kartu 4: Ubah password —— span dua kolom di layar lebar -->
    <div class="card" style="grid-column:1/-1">
        <div class="card-head"><h2>Ubah Password</h2></div>
        <div style="max-width:420px">
            <form method="post" action="<?= site_url('mahasiswa/profil/password') ?>">
                <?= csrf_field() ?>
                <div class="field">
                    <label>Password lama</label>
                    <div class="password-wrap">
                        <input type="password" name="current_password" id="mhs_current_password" required autocomplete="current-password">
                        <button type="button" class="password-toggle" data-target="mhs_current_password" aria-label="Tampilkan password">
                            <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="icon-eye-off" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><path stroke-linecap="round" stroke-linejoin="round" d="M1 1l22 22"/></svg>
                        </button>
                    </div>
                </div>
                <div class="field">
                    <label>Password baru</label>
                    <div class="password-wrap">
                        <input type="password" name="new_password" id="mhs_new_password" required minlength="6" autocomplete="new-password">
                        <button type="button" class="password-toggle" data-target="mhs_new_password" aria-label="Tampilkan password baru">
                            <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="icon-eye-off" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><path stroke-linecap="round" stroke-linejoin="round" d="M1 1l22 22"/></svg>
                        </button>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Ubah password</button>
                </div>
            </form>
        </div>
    </div>

</div>

<style>
@media (max-width:900px) { .dash-grid { grid-template-columns: 1fr !important; } }
@media (max-width:900px) { .dash-grid > .card[style*="grid-column"] { grid-column: unset !important; } }
</style>
