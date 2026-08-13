<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>
<div class="auth-shell">
    <div class="auth-card">
        <div class="auth-brand">
            <div class="mark">UK</div>
            <h1>Verifikasi OTP</h1>
            <p>Masukkan 6 digit kode dari email</p>
        </div>
        <?= view('partials/flash') ?>
        <form method="post" action="<?= site_url('otp-verify') ?>" id="otp-form">
            <?= csrf_field() ?>
            <div class="otp-row">
                <?php for ($i = 0; $i < 6; $i++): ?>
                    <input type="text" name="otp[]" maxlength="1" inputmode="numeric" pattern="[0-9]" required <?= $i === 0 ? 'autofocus' : '' ?>>
                <?php endfor; ?>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%">Verifikasi</button>
        </form>
        <p style="margin:20px 0 0;text-align:center;font-size:0.85rem">
            <a href="<?= site_url('forgot-password') ?>">Kirim ulang</a>
        </p>
    </div>
</div>
<script>
(() => {
  const inputs = [...document.querySelectorAll('.otp-row input')];
  inputs.forEach((input, i) => {
    input.addEventListener('input', () => {
      input.value = input.value.replace(/\D/g, '').slice(0, 1);
      if (input.value && i < inputs.length - 1) inputs[i + 1].focus();
    });
    input.addEventListener('keydown', (e) => {
      if (e.key === 'Backspace' && !input.value && i > 0) inputs[i - 1].focus();
    });
    input.addEventListener('paste', (e) => {
      const text = (e.clipboardData.getData('text') || '').replace(/\D/g, '').slice(0, 6);
      if (!text) return;
      e.preventDefault();
      text.split('').forEach((ch, idx) => { if (inputs[idx]) inputs[idx].value = ch; });
      inputs[Math.min(text.length, 5)].focus();
    });
  });
})();
</script>
<?= $this->endSection() ?>
