<?php
$flashMessages = [
    'success' => session()->getFlashdata('success'),
    'error'   => session()->getFlashdata('error'),
    'warning' => session()->getFlashdata('warning'),
];
$errors = session()->getFlashdata('errors');

if (! empty($errors)) {
    $errorList = array_map(
        static fn ($error) => is_array($error) ? implode(', ', $error) : (string) $error,
        (array) $errors
    );
    $flashMessages['error'] = count($errorList) > 1 
        ? implode("\n", $errorList)
        : (reset($errorList) ?: '');
}

$flashIcons = [
    'success' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>',
    'error'   => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>',
    'warning' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>',
];

$flashTitles = [
    'success' => 'Berhasil',
    'error'   => 'Perlu diperiksa',
    'warning' => 'Perhatian',
];

$flashStyles = [
    'success' => 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:text-emerald-300',
    'error'   => 'border-rose-200 bg-rose-50 text-rose-800 dark:border-rose-900/50 dark:bg-rose-950/30 dark:text-rose-300',
    'warning' => 'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-900/50 dark:bg-amber-950/30 dark:text-amber-300',
];

$iconStyles = [
    'success' => 'text-emerald-600 dark:text-emerald-400',
    'error'   => 'text-rose-600 dark:text-rose-400',
    'warning' => 'text-amber-600 dark:text-amber-400',
];

foreach ($flashMessages as $type => $message):
    if (! $message) { continue; }
    $lines = explode("\n", (string) $message);
    $singleLine = count($lines) === 1;
?>
    <div class="flash-message mb-4 flex items-start gap-3 rounded-xl border p-3.5 text-sm <?= $flashStyles[$type] ?? '' ?>"
         role="alert"
         data-toast-type="<?= esc($type) ?>"
         data-toast-title="<?= esc($flashTitles[$type] ?? 'Notifikasi') ?>"
         data-toast-message="<?= esc(($singleLine ? (string) $message : $lines[0]), 'attr') ?>">
        <span class="mt-0.5 shrink-0 <?= $iconStyles[$type] ?? '' ?>"><?= $flashIcons[$type] ?? '' ?></span>
        <div class="min-w-0 flex-1">
            <strong class="block font-extrabold"><?= esc($flashTitles[$type] ?? 'Notifikasi') ?></strong>
            <?php if ($singleLine): ?>
                <p class="mt-0.5 opacity-90"><?= esc((string) $message) ?></p>
            <?php else: ?>
                <ul class="mt-1.5 space-y-1 opacity-90">
                    <?php foreach ($lines as $line): ?>
                        <?php if (! empty($line)): ?>
                            <li class="flex gap-2"><span class="shrink-0">•</span><span><?= esc($line) ?></span></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>
