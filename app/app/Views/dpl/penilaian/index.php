<div class="card">
    <div class="card-head"><h2>Penilaian mahasiswa bimbingan</h2></div>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>NPM</th>
                    <th>Nama</th>
                    <th>Prodi</th>
                    <th>Status</th>
                    <th>Nilai</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($mahasiswa)): ?>
                <tr><td colspan="6" class="empty">Belum ada mahasiswa bimbingan. Admin menempatkan mahasiswa ke kelompok yang Anda bimbing.</td></tr>
            <?php else: ?>
                <?php foreach ($mahasiswa as $row): ?>
                    <tr>
                        <td class="font-mono"><?= esc($row['npm']) ?></td>
                        <td><?= esc($row['nama']) ?></td>
                        <td><?= esc($row['prodi'] ?? '-') ?></td>
                        <td>
                            <?php if (! empty($row['sudah_dinilai'])): ?>
                                <span class="stempel stempel-divalidasi">Sudah dinilai</span>
                            <?php else: ?>
                                <span class="stempel stempel-menunggu">Belum dinilai</span>
                            <?php endif; ?>
                        </td>
                        <td class="font-mono">
                            <?php if (! empty($row['sudah_dinilai'])): ?>
                                <?= esc(number_format((float) $row['nilai_akhir'], 2)) ?>
                                (<?= esc($row['grade'] ?? '-') ?>)
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td>
                            <a class="btn btn-primary btn-sm" href="<?= site_url('dpl/penilaian/' . $row['id']) ?>">
                                <?= ! empty($row['sudah_dinilai']) ? 'Ubah' : 'Nilai' ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
