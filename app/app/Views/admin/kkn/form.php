<?php $isEdit = ! empty($kelompok); ?>
<div class="card" style="max-width:720px">
    <div class="card-head"><h2><?= $isEdit ? 'Edit' : 'Tambah' ?> kelompok KKN</h2></div>
    <form method="post" action="<?= $isEdit ? site_url('admin/kkn/' . $kelompok['id']) : site_url('admin/kkn') ?>">
        <?= csrf_field() ?>
        <div class="form-grid">
            <div class="field">
                <label>Nama kelompok</label>
                <input type="text" name="nama_kelompok" placeholder="Kelompok 1 - Desa Waai" value="<?= esc(old('nama_kelompok', $kelompok['nama_kelompok'] ?? '')) ?>" required>
            </div>
            <div class="field">
                <label>Periode</label>
                <input type="text" name="periode" placeholder="2026 Genap" value="<?= esc(old('periode', $kelompok['periode'] ?? '')) ?>">
            </div>
            <div class="field">
                <label>DPL (Dosen Pembimbing Lapangan)</label>
                <select name="dpl_id" required>
                    <option value="">— Pilih DPL —</option>
                    <?php foreach ($dpl ?? [] as $row): ?>
                        <option value="<?= (int) $row['id'] ?>" <?= (string) old('dpl_id', $kelompok['dpl_id'] ?? '') === (string) $row['id'] ? 'selected' : '' ?>>
                            <?= esc($row['nama']) ?><?= ! empty($row['nidn']) ? ' · ' . esc($row['nidn']) : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="field-hint">DPL adalah dosen di lapangan. Buat akunnya dulu di menu DPL, lalu pilih di sini.</div>
            </div>
            <div class="field">
                <label>Lokasi penempatan</label>
                <select name="lokasi_id">
                    <option value="">— Pilih desa —</option>
                    <?php foreach ($lokasi ?? [] as $row): ?>
                        <option value="<?= (int) $row['id'] ?>" <?= (string) old('lokasi_id', $kelompok['lokasi_id'] ?? '') === (string) $row['id'] ? 'selected' : '' ?>>
                            <?= esc(format_alamat($row)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($isEdit && ! empty($mahasiswa)): ?>
            <div class="field" style="grid-column:1/-1">
                <label>Ketua kelompok</label>
                <select name="ketua_mahasiswa_id">
                    <option value="">— Pilih ketua —</option>
                    <?php foreach ($mahasiswa as $row): ?>
                        <option value="<?= (int) $row['id'] ?>" <?= (string) old('ketua_mahasiswa_id', $kelompok['ketua_mahasiswa_id'] ?? '') === (string) $row['id'] ? 'selected' : '' ?>>
                            <?= esc($row['nama']) ?> (<?= esc($row['npm']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="field-hint">Hanya ketua yang boleh menetapkan lokasi GPS lapangan kelompok</div>
            </div>
            <?php endif; ?>
            <div class="field">
                <label>Tanggal mulai</label>
                <input type="date" name="tanggal_mulai" value="<?= esc(old('tanggal_mulai', $kelompok['tanggal_mulai'] ?? '')) ?>">
            </div>
            <div class="field">
                <label>Tanggal selesai</label>
                <input type="date" name="tanggal_selesai" value="<?= esc(old('tanggal_selesai', $kelompok['tanggal_selesai'] ?? '')) ?>">
            </div>
            <div class="field" style="grid-column:1/-1">
                <label>Alamat penelitian</label>
                <textarea name="alamat_penelitian" rows="3" placeholder="Alamat lengkap lokasi penelitian KKN"><?= esc(old('alamat_penelitian', $kelompok['alamat_penelitian'] ?? '')) ?></textarea>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Simpan' : 'Tambah' ?></button>
            <a href="<?= site_url('admin/kkn') ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
