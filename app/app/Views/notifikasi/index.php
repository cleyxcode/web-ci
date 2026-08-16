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
            <div class="flex flex-col items-center justify-center py-14 px-6 text-center">
                <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-2xl bg-slate-100 text-slate-300 dark:bg-slate-800 dark:text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" class="h-8 w-8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>
                    </svg>
                </div>
                <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Belum ada notifikasi</p>
                <p class="mt-1 text-xs text-slate-400">Anda belum menerima notifikasi baru</p>
            </div>
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
