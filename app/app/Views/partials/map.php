<?php
$markers = $markers ?? [];
$mapId   = $mapId ?? 'map-' . bin2hex(random_bytes(3));
$zoom    = (int) ($zoom ?? 13);
$class   = $class ?? 'map-box';

$points = [];

foreach ($markers as $m) {
    if (empty($m['latitude']) || empty($m['longitude'])) {
        continue;
    }

    $desa  = $m['nama_desa'] ?? '';
    $ketua = $m['nama_ketua'] ?? '';
    $nama  = $m['nama_kelompok'] ?? 'Lokasi KKN';
    $popup = '<strong>' . esc($nama) . '</strong>';

    if ($desa !== '') {
        $popup .= '<br>' . esc($desa);
    }

    if ($ketua !== '') {
        $popup .= '<br>Ketua: ' . esc($ketua);
    }

    $points[] = [
        'lat'   => (float) $m['latitude'],
        'lng'   => (float) $m['longitude'],
        'popup' => $popup,
    ];
}
?>
<?php if ($points === []): ?>
    <div class="flex flex-col items-center justify-center py-10 px-6 text-center">
        <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-2xl bg-slate-100 text-slate-300 dark:bg-slate-800 dark:text-slate-600">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" class="h-8 w-8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
            </svg>
        </div>
        <p class="text-sm font-bold text-slate-500 dark:text-slate-400"><?= esc($empty ?? 'Belum ada lokasi GPS.') ?></p>
    </div>
<?php else: ?>
    <div id="<?= esc($mapId) ?>" class="<?= esc($class) ?>"
         data-map="1"
         data-zoom="<?= $zoom ?>"
         data-points="<?= esc(json_encode($points), 'attr') ?>"></div>
<?php endif; ?>
