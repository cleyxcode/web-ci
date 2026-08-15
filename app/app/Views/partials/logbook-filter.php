<?php
$filterRoute = $filterRoute ?? current_url();
$filterPeriod = $filterPeriod ?? 'minggu';
$filterDate = $filterDate ?? date('Y-m-d');
$isDefault = $filterPeriod === 'minggu' && $filterDate === date('Y-m-d');
?>
<div class="dashboard-filter-card">
    <div class="dashboard-filter-copy">
        <span class="dashboard-filter-kicker">Aktivitas logbook</span>
        <strong><?= esc($filterLabel ?? 'Pilih rentang waktu') ?></strong>
        <small>Gunakan tanggal acuan untuk melihat logbook harian atau satu minggu.</small>
    </div>
    <form method="get" action="<?= esc($filterRoute) ?>" class="dashboard-filter">
        <div class="field">
            <label for="logbook-filter-period">Rentang</label>
            <select id="logbook-filter-period" name="periode">
                <option value="hari" <?= $filterPeriod === 'hari' ? 'selected' : '' ?>>Per hari</option>
                <option value="minggu" <?= $filterPeriod === 'minggu' ? 'selected' : '' ?>>Per minggu</option>
                <option value="semua" <?= $filterPeriod === 'semua' ? 'selected' : '' ?>>Semua logbook</option>
            </select>
        </div>
        <div class="field">
            <label for="logbook-filter-date">Tanggal acuan</label>
            <input id="logbook-filter-date" type="date" name="tanggal" value="<?= esc($filterDate) ?>">
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Terapkan</button>
        <?php if (! $isDefault): ?>
            <a href="<?= esc($filterRoute) ?>" class="btn btn-secondary btn-sm">Reset</a>
        <?php endif; ?>
    </form>
</div>
