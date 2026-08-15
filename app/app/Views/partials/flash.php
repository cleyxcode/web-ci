<?php
$flashMessages = [
    'success' => session()->getFlashdata('success'),
    'error'   => session()->getFlashdata('error'),
    'warning' => session()->getFlashdata('warning'),
];
$errors = session()->getFlashdata('errors');

if (! empty($errors)) {
    $flashMessages['error'] = implode(' • ', array_map(
        static fn ($error) => is_array($error) ? implode(', ', $error) : (string) $error,
        (array) $errors
    ));
}

$flashTitles = [
    'success' => 'Berhasil',
    'error'   => 'Perlu diperiksa',
    'warning' => 'Perhatian',
];

foreach ($flashMessages as $type => $message):
    if (! $message) {
        continue;
    }
?>
    <div class="flash-message" role="alert"
         data-toast-type="<?= esc($type) ?>"
         data-toast-title="<?= esc($flashTitles[$type] ?? 'Notifikasi') ?>"
         data-toast-message="<?= esc((string) $message, 'attr') ?>">
        <?= esc((string) $message) ?>
    </div>
<?php endforeach; ?>
