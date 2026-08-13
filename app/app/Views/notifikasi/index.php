<div class="card" style="max-width:720px">
    <div class="card-head">
        <h2>Semua notifikasi</h2>
        <?php if (($unreadCount ?? 0) > 0): ?>
            <form method="post" action="<?= site_url('notifikasi/read-all') ?>">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-secondary btn-sm">Tandai semua dibaca</button>
            </form>
        <?php endif; ?>
    </div>
    <div class="notif-list-page">
        <?php if (empty($items)): ?>
            <p class="empty">Belum ada notifikasi.</p>
        <?php else: ?>
            <?php foreach ($items as $n): ?>
                <div class="notif-item-page <?= $n['is_read'] ? '' : 'unread' ?> notif-type-<?= esc($n['type']) ?>">
                    <strong><?= esc($n['judul']) ?></strong>
                    <p><?= esc($n['pesan']) ?></p>
                    <small><?= format_tanggal($n['created_at'] ?? null) ?></small>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
