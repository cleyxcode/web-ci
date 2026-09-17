<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>
<div class="auth-shell">
    <div class="auth-card">
        <div class="auth-brand">
            <div class="mark">UK</div>
            <h1>Daftar Akun</h1>
            <p>Buat akun mahasiswa KKN Tematik FILKOM</p>
        </div>

        <?= view('partials/flash') ?>

        <form method="post" action="<?= site_url('register') ?>" novalidate>
            <?= csrf_field() ?>

            <div class="field">
                <label for="nama">Nama lengkap</label>
                <input type="text" id="nama" name="nama"
                       value="<?= esc(old('nama')) ?>"
                       required autofocus autocomplete="name"
                       placeholder="Sesuai kartu identitas">
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email"
                       value="<?= esc(old('email')) ?>"
                       required autocomplete="email"
                       placeholder="Alamat email aktif Anda">
            </div>

            <div class="field">
                <label for="password">Password</label>
                <div class="password-wrap">
                    <input type="password" id="password" name="password"
                           required minlength="6" autocomplete="new-password"
                           placeholder="Minimal 6 karakter">
                    <button type="button" class="password-toggle" data-target="password" aria-label="Tampilkan password" aria-pressed="false">
                        <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        <svg class="icon-eye-off" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M1 1l22 22"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="field">
                <label for="password_confirm">Konfirmasi password</label>
                <div class="password-wrap">
                    <input type="password" id="password_confirm" name="password_confirm"
                           required minlength="6" autocomplete="new-password"
                           placeholder="Ulangi password">
                    <button type="button" class="password-toggle" data-target="password_confirm" aria-label="Tampilkan konfirmasi password" aria-pressed="false">
                        <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        <svg class="icon-eye-off" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M1 1l22 22"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="field-hint">
                NPM, program studi, dan nomor HP dapat dilengkapi setelah masuk di halaman <strong>Profil</strong>.
            </div>

            <button type="submit" class="btn btn-primary">
                Daftar
            </button>
        </form>

        <p>
            Sudah punya akun? <a href="<?= site_url('login') ?>">Masuk di sini</a>
        </p>
    </div>
</div>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
<?= $this->endSection() ?>
