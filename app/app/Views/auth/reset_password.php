<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>
<div class="auth-shell">
    <div class="auth-card">
        <div class="auth-brand">
            <div class="mark">UK</div>
            <h1>Password Baru</h1>
            <p>Buat password minimal 6 karakter</p>
        </div>
        <?= view('partials/flash') ?>
        <form method="post" action="<?= site_url('reset-password') ?>">
            <?= csrf_field() ?>
            <div class="field">
                <label for="password">Password baru</label>
                <input type="password" id="password" name="password" required minlength="6" autofocus>
            </div>
            <div class="field">
                <label for="password_confirm">Konfirmasi password</label>
                <input type="password" id="password_confirm" name="password_confirm" required minlength="6">
            </div>
            <button type="submit" class="btn btn-primary">Simpan Password</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
