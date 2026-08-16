<div class="card">
    <div class="card-head"><h2>Penilaian mahasiswa bimbingan</h2></div>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>NPM</th>
                    <th>Nama</th>
                    <th>Kelompok</th>
                    <th>Prodi</th>
                    <th>Status</th>
                    <th>Nilai</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($mahasiswa)): ?>
                <tr>
                    <td colspan="7">
                        <div class="flex flex-col items-center justify-center py-14 px-6 text-center">
                            <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-2xl bg-slate-100 text-slate-300 dark:bg-slate-800 dark:text-slate-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" class="h-8 w-8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Belum ada mahasiswa bimbingan</p>
                            <p class="mt-1 text-xs text-slate-400 max-w-xs">Admin kampus menempatkan mahasiswa ke kelompok yang Anda bimbing</p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($mahasiswa as $row): ?>
                    <tr>
                        <td class="font-mono"><?= esc($row['npm']) ?></td>
                        <td><?= esc($row['nama']) ?></td>
                        <td><?= esc($row['nama_kelompok'] ?? '-') ?></td>
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
