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
    <p class="empty" style="padding:16px 0"><?= esc($empty ?? 'Belum ada lokasi GPS.') ?></p>
<?php else: ?>
    <div id="<?= esc($mapId) ?>" class="<?= esc($class) ?>"
         data-map="1"
         data-zoom="<?= $zoom ?>"
         data-points="<?= esc(json_encode($points), 'attr') ?>"></div>
<?php endif; ?>
