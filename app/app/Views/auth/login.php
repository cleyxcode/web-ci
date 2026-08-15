<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>
<div class="auth-shell">
    <div class="auth-card">
        <div class="auth-brand">
            <div class="mark">UK</div>
            <h1>KKN Tematik UKIM</h1>
            <p>Masuk ke sistem monitoring lapangan</p>
        </div>

        <?= view('partials/flash') ?>

        <form method="post" action="<?= site_url('login') ?>">
            <?= csrf_field() ?>
            <div class="field">
                <label for="login">Username, email, NPM, atau NIDN</label>
                <input type="text" id="login" name="login" value="<?= esc(old('login')) ?>" required autofocus autocomplete="username" placeholder="Masukkan identitas login Anda">
                <div class="field-hint">Mahasiswa gunakan NPM, DPL gunakan NIDN.</div>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <div class="password-wrap">
                    <input type="password" id="password" name="password" required autocomplete="current-password">
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
            <button type="submit" class="btn btn-primary" style="width:100%;margin-top:8px">Masuk</button>
        </form>
        <p style="margin:20px 0 0;text-align:center;font-size:0.85rem;color:var(--abu-karang)">
            <a href="<?= site_url('forgot-password') ?>">Lupa password?</a>
            <br>
            <span style="display:inline-block;margin-top:8px;color:var(--tinta-redup);font-size:0.78rem">Akun mahasiswa dibuat oleh admin kampus.</span>
        </p>
    </div>
</div>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
<?= $this->endSection() ?>
