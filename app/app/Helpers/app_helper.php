<?php

if (! function_exists('current_user')) {
    function current_user(): ?array
    {
        $session = session();

        if (! $session->get('logged_in')) {
            return null;
        }

        return [
            'id'       => $session->get('user_id'),
            'nama'     => $session->get('nama'),
            'username' => $session->get('username'),
            'email'    => $session->get('email'),
            'role'     => $session->get('role'),
            'foto'     => $session->get('foto'),
        ];
    }
}

if (! function_exists('stempel_class')) {
    function stempel_class(?string $status): string
    {
        return match ($status) {
            'menunggu'  => 'stempel stempel-menunggu',
            'divalidasi'=> 'stempel stempel-divalidasi',
            'diterima'  => 'stempel stempel-diterima',
            'ditolak'   => 'stempel stempel-ditolak',
            default     => 'stempel',
        };
    }
}

if (! function_exists('stempel_label')) {
    function stempel_label(?string $status): string
    {
        return match ($status) {
            'menunggu'   => 'Menunggu',
            'divalidasi' => 'Divalidasi',
            'diterima'   => 'Diterima',
            'ditolak'    => 'Ditolak',
            default      => ucfirst((string) $status),
        };
    }
}

if (! function_exists('grade_color')) {
    function grade_color(?string $grade): string
    {
        return match ($grade) {
            'A'       => 'text-[#2D7A4F] font-mono font-bold',
            'B'       => 'text-[#1B6B8A] font-mono font-bold',
            'BC'      => 'text-[#C4920A] font-mono font-bold',
            'C'       => 'text-[#C4920A] font-mono font-bold',
            'D'       => 'text-[#B83232] font-mono font-bold',
            default   => 'text-[#6B6560] font-mono',
        };
    }
}

// Alias lama tetap dipertahankan agar view/integrasi pihak ketiga tidak rusak.
if (! function_exists('grade_class')) {
    function grade_class(?string $grade): string
    {
        return grade_color($grade);
    }
}

if (! function_exists('panel_menus')) {
    function panel_menus(string $role): array
    {
        return match ($role) {
            'admin' => [
                ['label' => 'Dashboard', 'url' => '/admin/dashboard', 'icon' => 'home', 'mobile' => true],
                ['label' => 'Mahasiswa', 'url' => '/admin/mahasiswa', 'icon' => 'users', 'mobile' => true],
                ['label' => 'Dosen Pembimbing', 'url' => '/admin/dpl', 'icon' => 'academic', 'mobile' => true],
                ['label' => 'Kelompok KKN', 'url' => '/admin/kkn', 'icon' => 'group', 'mobile' => true],
                ['label' => 'Lokasi KKN', 'url' => '/admin/lokasi', 'icon' => 'map'],
                ['label' => 'Laporan', 'url' => '/admin/laporan', 'icon' => 'doc', 'mobile' => true],
                ['label' => 'Logbook', 'url' => '/admin/logbook', 'icon' => 'book', 'mobile' => true],
                ['label' => 'Evaluasi', 'url' => '/admin/evaluasi', 'icon' => 'clipboard'],
                ['label' => 'Pengumuman', 'url' => '/admin/pengumuman', 'icon' => 'bell'],
                ['label' => 'Audit Trail', 'url' => '/admin/audit', 'icon' => 'history'],
                ['label' => 'Pengaturan', 'url' => '/admin/profil', 'icon' => 'settings'],
            ],
            'dpl' => [
                ['label' => 'Dashboard', 'url' => '/dpl/dashboard', 'icon' => 'home', 'mobile' => true],
                ['label' => 'Monitoring', 'url' => '/dpl/monitoring', 'icon' => 'eye', 'mobile' => true],
                ['label' => 'Validasi Logbook', 'url' => '/dpl/logbook', 'icon' => 'check', 'mobile' => true],
                ['label' => 'Review Laporan', 'url' => '/dpl/laporan', 'icon' => 'doc'],
                ['label' => 'Penilaian', 'url' => '/dpl/penilaian', 'icon' => 'star', 'mobile' => true],
                ['label' => 'Evaluasi Mhs', 'url' => '/dpl/evaluasi', 'icon' => 'chat', 'mobile' => true],
                ['label' => 'Export', 'url' => '/dpl/export', 'icon' => 'download'],
            ],
            'mahasiswa' => [
                ['label' => 'Dashboard', 'url' => '/mahasiswa/dashboard', 'icon' => 'home', 'mobile' => true],
                ['label' => 'Tim KKN', 'url' => '/mahasiswa/tim', 'icon' => 'group', 'mobile' => true],
                ['label' => 'Logbook', 'url' => '/mahasiswa/logbook', 'icon' => 'book', 'mobile' => true],
                ['label' => 'Laporan', 'url' => '/mahasiswa/laporan', 'icon' => 'upload'],
                ['label' => 'Nilai', 'url' => '/mahasiswa/nilai', 'icon' => 'star', 'mobile' => true],
                ['label' => 'Evaluasi', 'url' => '/mahasiswa/evaluasi', 'icon' => 'chat', 'mobile' => true],
                ['label' => 'Profil', 'url' => '/mahasiswa/profil', 'icon' => 'user'],
            ],
            default => [],
        };
    }
}

if (! function_exists('format_alamat')) {
    function format_alamat(?array $lokasi): string
    {
        if (! $lokasi) {
            return '-';
        }

        $parts = array_filter([
            $lokasi['nama_desa'] ?? '',
            $lokasi['kecamatan'] ?? '',
            $lokasi['kabupaten'] ?? '',
        ]);

        return $parts !== [] ? implode(', ', $parts) : '-';
    }
}

if (! function_exists('format_tanggal')) {
    function format_tanggal(?string $date): string
    {
        if (! $date) {
            return '-';
        }

        return date('d M Y', strtotime($date));
    }
}

if (! function_exists('upload_storage_dir')) {
    /**
     * Persistent upload root (outside public_html so Hostinger redeploys don't wipe files).
     */
    function upload_storage_dir(): string
    {
        return rtrim(WRITEPATH, '/\\') . DIRECTORY_SEPARATOR . 'uploads';
    }
}

if (! function_exists('resolve_uploaded_file')) {
    /**
     * Resolve a stored relative path (e.g. logbook/file.jpg) to an absolute file.
     * Prefers writable/uploads, falls back to legacy public/uploads.
     */
    function resolve_uploaded_file(string $relativePath): ?string
    {
        $relativePath = str_replace(['\\', "\0"], ['/', ''], $relativePath);
        $relativePath = ltrim($relativePath, '/');

        if ($relativePath === '' || str_contains($relativePath, '..')) {
            return null;
        }

        $candidates = [
            upload_storage_dir() . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath),
            rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath),
        ];

        foreach ($candidates as $candidate) {
            $real = realpath($candidate);
            if ($real === false || ! is_file($real)) {
                continue;
            }

            $allowedRoots = array_filter([
                realpath(upload_storage_dir()),
                realpath(rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . 'uploads'),
            ]);

            foreach ($allowedRoots as $root) {
                if (str_starts_with($real, rtrim($root, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR) || $real === $root) {
                    return $real;
                }
            }
        }

        return null;
    }
}

if (! function_exists('delete_uploaded_file')) {
    function delete_uploaded_file(?string $relativePath): void
    {
        if ($relativePath === null || trim($relativePath) === '') {
            return;
        }

        $absolute = resolve_uploaded_file($relativePath);
        if ($absolute !== null) {
            @unlink($absolute);
        }
    }
}

if (! function_exists('upload_file')) {
    function upload_file($file, string $folder, array $allowed = ['jpg', 'jpeg', 'png', 'pdf'], int $maxKb = 5120): ?string
    {
        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }

        $ext = strtolower((string) $file->getExtension());

        // Fallback ke ekstensi dari nama file asli jika getExtension() kosong
        if ($ext === '' && method_exists($file, 'getClientExtension')) {
            $ext = strtolower((string) $file->getClientExtension());
        }

        if ($ext === '' || ! in_array($ext, $allowed, true)) {
            return null;
        }

        if ($file->getSizeByUnit('kb') > $maxKb) {
            return null;
        }

        $newName = $file->getRandomName();
        $folder  = trim($folder, '/');
        $path    = upload_storage_dir() . DIRECTORY_SEPARATOR . $folder;

        if (! is_dir($path) && ! mkdir($path, 0755, true) && ! is_dir($path)) {
            return null;
        }

        try {
            $file->move($path, $newName);
        } catch (Throwable $e) {
            log_message('error', 'Upload move failed: {message}', ['message' => $e->getMessage()]);
            return null;
        }

        return $folder . '/' . $newName;
    }
}

if (! function_exists('upload_files')) {
    /** @return list<string> */
    function upload_files(array $files, string $folder, array $allowed = ['jpg', 'jpeg', 'png'], int $maxKb = 5120, int $maxFiles = 3): array
    {
        $uploaded = [];
        foreach (array_slice($files, 0, $maxFiles) as $file) {
            $path = upload_file($file, $folder, $allowed, $maxKb);
            if ($path !== null) {
                $uploaded[] = $path;
            }
        }
        return $uploaded;
    }
}

if (! function_exists('stored_files')) {
    /** @return list<string> */
    function stored_files(?string $value): array
    {
        if ($value === null || trim($value) === '') {
            return [];
        }

        $decoded = json_decode($value, true);
        if (is_array($decoded)) {
            $paths = array_values(array_filter($decoded, static fn ($item): bool => is_string($item) && trim($item) !== ''));
        } else {
            $paths = [trim($value)];
        }

        return array_values(array_unique(array_map(static function (string $path): string {
            $path = str_replace('\\', '/', $path);
            $path = ltrim($path, '/');
            if (str_starts_with($path, 'uploads/')) {
                $path = substr($path, strlen('uploads/'));
            }

            return $path;
        }, $paths)));
    }
}

if (! function_exists('uploaded_url')) {
    /**
     * URL publik (via UploadsController) untuk path relatif di DB.
     * Tetap mengembalikan URL meski file belum ketemu di disk, agar link tidak hilang.
     */
    function uploaded_url(?string $relativePath): ?string
    {
        $files = stored_files($relativePath);
        $path  = $files[0] ?? null;
        if ($path === null) {
            return null;
        }

        $segments = array_map('rawurlencode', explode('/', $path));

        return site_url('uploads/' . implode('/', $segments));
    }
}

if (! function_exists('uploaded_file_exists')) {
    function uploaded_file_exists(?string $relativePath): bool
    {
        $files = stored_files($relativePath);
        $path  = $files[0] ?? null;

        return $path !== null && resolve_uploaded_file($path) !== null;
    }
}

if (! function_exists('is_image_path')) {
    function is_image_path(string $relativePath): bool
    {
        $ext = strtolower(pathinfo($relativePath, PATHINFO_EXTENSION));

        return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
    }
}
