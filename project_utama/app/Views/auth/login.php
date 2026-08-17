<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>
<div class="auth-shell">
<div class="auth-card auth-card-slide">
    <div class="auth-brand">
        <div class="mark" style="width:52px;height:52px;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.5" class="h-7 w-7">
                <path stroke-linecap="round" stroke-linejoin="round" d="M22 10 12 5 2 10l10 5 10-5zm0 0v6M6 12.5V17c0 1.5 2.7 3 6 3s6-1.5 6-3v-4.5"/>
            </svg>
        </div>
        <h1>Selamat datang kembali</h1>
        <p>Masuk ke sistem monitoring KKN Tematik FILKOM</p>
    </div>

    <?= view('partials/flash') ?>

    <form method="post" action="<?= site_url('login') ?>">
        <?= csrf_field() ?>
        <div class="field">
            <label for="login">
                <span style="display:flex;align-items:center;gap:6px;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display:inline;width:15px;height:15px;color:#94a3b8;"><path stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/></svg>
                    Username / NPM / NIDN / Email
                </span>
            </label>
            <input type="text" id="login" name="login" value="<?= esc(old('login')) ?>" required autofocus autocomplete="username" placeholder="Masukkan identitas login Anda">
            <div class="field-hint">Mahasiswa gunakan NPM, DPL gunakan NIDN.</div>
        </div>
        <div class="field">
            <label for="password">
                <span style="display:flex;align-items:center;gap:6px;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display:inline;width:15px;height:15px;color:#94a3b8;"><rect x="3" y="11" width="18" height="11" rx="2"/><path stroke-linecap="round" d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    Password
                </span>
            </label>
            <div class="password-wrap">
                <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="Masukkan password Anda">
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
        <div style="margin-bottom:20px;display:flex;justify-content:flex-end;">
            <a href="<?= site_url('forgot-password') ?>" style="font-size:12px;font-weight:700;color:#7c3aed;">Lupa password?</a>
        </div>
        <button type="submit" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/>
            </svg>
            Masuk ke Sistem
        </button>
    </form>
    <p class="auth-links">
        <span>Akun mahasiswa dibuat oleh admin kampus. Hubungi admin jika belum memiliki akun.</span>
    </p>
</div>
</div>
<?= $this->endSection() ?>
