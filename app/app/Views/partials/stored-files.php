<?php
/**
 * Preview file upload (gambar / PDF) untuk logbook & laporan.
 *
 * @var string|null $value  Path tunggal, JSON array, atau null
 * @var string      $empty  Teks jika kosong
 */
$files = stored_files($value ?? null);
$empty = $empty ?? '-';
?>
<?php if ($files === []): ?>
    <span class="text-slate-400"><?= esc($empty) ?></span>
<?php else: ?>
    <div class="file-preview-list">
        <?php foreach ($files as $file): ?>
            <?php
            $url     = uploaded_url($file);
            $exists  = uploaded_file_exists($file);
            $isImage = is_image_path($file);
            $label   = ! $exists ? 'File tidak ditemukan' : ($isImage ? 'Lihat foto' : 'Buka PDF');
            ?>
            <?php if ($url === null): ?>
                <span class="text-slate-400" title="<?= esc($file) ?>">—</span>
            <?php elseif (! $exists): ?>
                <span class="file-preview-missing" title="<?= esc($file) ?>">File hilang</span>
            <?php else: ?>
                <a class="file-preview-item<?= $isImage ? ' is-image' : '' ?>" href="<?= esc($url) ?>" target="_blank" rel="noopener" title="<?= esc($label) ?>">
                    <?php if ($isImage): ?>
                        <img src="<?= esc($url) ?>" alt="Dokumentasi" loading="lazy" width="64" height="64">
                    <?php else: ?>
                        <span class="file-preview-pdf">PDF</span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
