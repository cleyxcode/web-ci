<?php
$isEdit   = ! empty($mahasiswa);
$action   = $isEdit ? site_url('admin/mahasiswa/' . $mahasiswa['id']) : site_url('admin/mahasiswa');
$errors   = session()->getFlashdata('errors') ?? [];
?>

<div>
    <!-- Back + title -->
    <div>
        <a href="<?= site_url('admin/mahasiswa') ?>"

           title="Kembali ke daftar">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1><?= $isEdit ? 'Edit Akun Mahasiswa' : 'Tambah Mahasiswa Baru' ?></h1>
            <p><?= $isEdit ? 'Ubah data profil & akun login mahasiswa' : 'Buat akun baru dan data studi mahasiswa' ?></p>
        </div>
    </div>

    <?php if (! empty($errors)): ?>
        <div>
            <strong>Terdapat kesalahan:</strong>
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= esc($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= $action ?>" autocomplete="off">
        <?= csrf_field() ?>

        <!-- ── Seksi 1: Informasi Dasar ─────────────────────── -->
        <div class="card">
            <div class="card-head">
                <h2>
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/></svg>
                    Informasi Pribadi
                </h2>
            </div>
            <div class="form-grid">
                <div class="field">
                    <label for="f_nama">Nama Lengkap <span>*</span></label>
                    <input type="text" id="f_nama" name="nama" autocomplete="off"
                           value="<?= esc(old('nama', $mahasiswa['nama'] ?? '')) ?>"
                           placeholder="Nama lengkap sesuai KTP" required>
                </div>
                <div class="field">
                    <label for="f_npm">NPM <span>*</span></label>
                    <input type="text" id="f_npm" name="npm" class="font-mono"
                           value="<?= esc(old('npm', ($mahasiswa['npm'] ?? '') !== '' && !str_starts_with($mahasiswa['npm'] ?? '', 'TEMP_') ? $mahasiswa['npm'] : '')) ?>"
                           placeholder="Nomor Pokok Mahasiswa" required maxlength="20" autocomplete="off">
                </div>
                <div class="field">
                    <label for="f_prodi">Program Studi</label>
                    <input type="text" id="f_prodi" name="prodi"
                           value="<?= esc(old('prodi', $mahasiswa['prodi'] ?? '')) ?>"
                           placeholder="Misal: Teknik Informatika">
                </div>
                <div class="field">
                    <label for="f_no_hp">No. HP / WhatsApp</label>
                    <input type="tel" id="f_no_hp" name="no_hp"
                           value="<?= esc(old('no_hp', $mahasiswa['no_hp'] ?? '')) ?>"
                           placeholder="08xxxxxxxxxx" maxlength="20">
                </div>
                <div class="field">
                    <label for="f_kelompok">Kelompok KKN</label>
                    <select id="f_kelompok" name="kelompok_id">
                        <option value="">— Belum ditempatkan —</option>
                        <?php foreach ($kelompok ?? [] as $k): ?>
                            <option value="<?= (int) $k['id'] ?>" <?= (string) old('kelompok_id', $mahasiswa['kelompok_id'] ?? '') === (string) $k['id'] ? 'selected' : '' ?>>
                                <?= esc($k['nama_kelompok']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- ── Seksi 2: Akun Login ───────────────────────────── -->
        <div class="card">
            <div class="card-head">
                <h2>
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><rect x="3" y="11" width="18" height="11" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    Akun Login
                </h2>
            </div>
            <div class="form-grid">
                <div class="field">
                    <label for="f_username">Username <span>*</span></label>
                    <input type="text" id="f_username" name="username" autocomplete="new-password"
                           value="<?= esc(old('username', $mahasiswa['username'] ?? '')) ?>"
                           placeholder="Username untuk login" required>
                </div>
                <div class="field">
                    <label for="f_email">Email <span>*</span></label>
                    <input type="email" id="f_email" name="email" autocomplete="off"
                           value="<?= esc(old('email', $mahasiswa['email'] ?? '')) ?>"
                           placeholder="alamat@email.com" required>
                </div>
                <div class="field">
                    <label for="f_password">
                        Password <?= $isEdit ? '<span>(biarkan kosong jika tidak diubah)</span>' : '<span>*</span>' ?>
                    </label>
                    <div class="password-wrap">
                        <input type="password" id="f_password" name="password" autocomplete="new-password"
                               <?= $isEdit ? '' : 'required' ?> minlength="6" placeholder="<?= $isEdit ? 'Isi untuk mengubah password' : 'Min. 6 karakter' ?>">
                        <button type="button" class="password-toggle" data-target="f_password" aria-label="Tampilkan password">
                            <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="icon-eye-off" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><path stroke-linecap="round" stroke-linejoin="round" d="M1 1l22 22"/></svg>
                        </button>
                    </div>
                </div>
                <?php if (! $isEdit): ?>
                <div class="field">
                    <label for="f_password_confirm">Konfirmasi Password <span>*</span></label>
                    <div class="password-wrap">
                        <input type="password" id="f_password_confirm" name="password_confirm"
                               required minlength="6" placeholder="Ulangi password" autocomplete="new-password">
                        <button type="button" class="password-toggle" data-target="f_password_confirm" aria-label="Tampilkan konfirmasi">
                            <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="icon-eye-off" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><path stroke-linecap="round" stroke-linejoin="round" d="M1 1l22 22"/></svg>
                        </button>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ── Tombol Aksi ───────────────────────────────────── -->
        <div>
            <button type="submit" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <?= $isEdit ? 'Simpan Perubahan' : 'Tambah Mahasiswa' ?>
            </button>
            <a href="<?= site_url('admin/mahasiswa') ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
