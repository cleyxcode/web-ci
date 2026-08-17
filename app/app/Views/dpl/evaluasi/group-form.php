<div class="mx-auto max-w-6xl space-y-6">
    <section class="hero-strip"><p class="periode">Evaluasi satu kelompok</p><h2><?= esc($kelompok['nama_kelompok'] ?? '-') ?></h2><span>Isi rating untuk seluruh anggota kelompok. Data dikirim ke panel admin setelah disimpan.</span></section>
    <?php if (! empty(session('errors'))): ?><div class="alert alert-danger"><ul class="list-inside list-disc"><?php foreach ((array) session('errors') as $error): ?><li><?= esc((string) $error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <?php if (empty($criteria)): ?><section class="card"><p class="empty">Kriteria evaluasi belum tersedia. Hubungi admin.</p></section><?php else: ?>
    <form method="post" action="<?= site_url('dpl/evaluasi/kelompok/' . (int) $kelompok['id']) ?>" class="space-y-6"><?= csrf_field() ?>
        <?php foreach ($mahasiswa as $student): $studentId = (int) $student['id']; $oldStudent = $evaluasi[$studentId] ?? null; $saved = []; $decoded = json_decode((string) ($oldStudent['detail_evaluasi'] ?? '[]'), true); foreach (is_array($decoded) ? $decoded : [] as $item) $saved[(int) ($item['id'] ?? 0)] = (int) ($item['rating'] ?? 0); ?>
        <section class="card"><div class="card-head"><div><h2><?= esc($student['nama']) ?></h2><p class="text-xs text-slate-500"><?= esc($student['npm']) ?></p></div><span class="stempel <?= $oldStudent ? 'stempel-divalidasi' : 'stempel-menunggu' ?>"><?= $oldStudent ? 'Sudah dinilai' : 'Belum dinilai' ?></span></div>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3"><?php foreach ($criteria as $criterion): $criterionId = (int) $criterion['id']; $value = $saved[$criterionId] ?? 0; ?><label class="field mb-0"><span><?= esc($criterion['nama']) ?></span><select name="ratings[<?= $studentId ?>][<?= $criterionId ?>]" required><option value="">Pilih 1–5</option><?php for ($i=1;$i<=5;$i++): ?><option value="<?= $i ?>" <?= $value === $i ? 'selected' : '' ?>><?= $i ?> / 5</option><?php endfor; ?></select></label><?php endforeach; ?></div>
            <div class="field mt-4 mb-0"><label>Catatan mahasiswa ini</label><textarea name="komentar[<?= $studentId ?>]" rows="3" maxlength="2000"><?= esc($oldStudent['komentar'] ?? '') ?></textarea></div>
        </section><?php endforeach; ?>
        <div class="form-actions"><a href="<?= site_url('dpl/evaluasi') ?>" class="btn btn-secondary">Batal</a><button class="btn btn-primary" type="submit">Kirim evaluasi kelompok</button></div>
    </form><?php endif; ?>
</div>
