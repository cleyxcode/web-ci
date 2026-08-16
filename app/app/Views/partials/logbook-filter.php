<?php
$filterRoute  = $filterRoute ?? current_url();
$filterPeriod = $filterPeriod ?? 'minggu';
$filterDate   = $filterDate ?? date('Y-m-d');
$isDefault    = $filterPeriod === 'minggu' && $filterDate === date('Y-m-d');
?>
<div class="dashboard-filter-card">
    <div class="dashboard-filter-copy">
        <span class="dashboard-filter-kicker">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="inline h-3.5 w-3.5 mr-1"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19V5M4 19h16M8 17V10M12 17V7M16 17v-4"/></svg>
            Aktivitas logbook
        </span>
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
        <button type="submit" class="btn btn-primary btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0z"/></svg>
            Terapkan
        </button>
        <?php if (! $isDefault): ?>
            <a href="<?= esc($filterRoute) ?>" class="btn btn-secondary btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                Reset
            </a>
        <?php endif; ?>
    </form>
</div>
