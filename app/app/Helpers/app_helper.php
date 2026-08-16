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
    function stempel_class(string $status): string
    {
        return match ($status) {
            'menunggu'   => 'stempel stempel-menunggu',
            'divalidasi' => 'stempel stempel-divalidasi',
            'diterima'   => 'stempel stempel-diterima',
            'ditolak'    => 'stempel stempel-ditolak',
            default      => 'stempel',
        };
    }
}

if (! function_exists('stempel_label')) {
    function stempel_label(string $status): string
    {
        return match ($status) {
            'menunggu'   => 'Menunggu',
            'divalidasi' => 'Divalidasi',
            'diterima'   => 'Diterima',
            'ditolak'    => 'Ditolak',
            default      => ucfirst($status),
        };
    }
}

if (! function_exists('grade_class')) {
    function grade_class(?string $grade): string
    {
        return match ($grade) {
            'A', 'B'  => 'text-[#2D7A4F] font-mono font-bold',
            'BC', 'C' => 'text-[#C4920A] font-mono font-bold',
            'D'       => 'text-[#B83232] font-mono font-bold',
            default   => 'text-[#6B6560] font-mono',
        };
    }
}

if (! function_exists('panel_menus')) {
    function panel_menus(string $role): array
    {
        return match ($role) {
            'admin' => [
                ['label' => 'Dashboard', 'url' => '/admin/dashboard', 'icon' => 'home', 'mobile' => true],
                ['label' => 'Mahasiswa', 'url' => '/admin/mahasiswa', 'icon' => 'users', 'mobile' => true],
                ['label' => 'DPL', 'url' => '/admin/dpl', 'icon' => 'academic', 'mobile' => true],
                ['label' => 'Kelompok KKN', 'url' => '/admin/kkn', 'icon' => 'group', 'mobile' => true],
                ['label' => 'Lokasi KKN', 'url' => '/admin/lokasi', 'icon' => 'map'],
                ['label' => 'Laporan', 'url' => '/admin/laporan', 'icon' => 'doc', 'mobile' => true],
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

if (! function_exists('upload_file')) {
    function upload_file($file, string $folder, array $allowed = ['jpg', 'jpeg', 'png', 'pdf'], int $maxKb = 5120): ?string
    {
        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }

        $ext = strtolower($file->getExtension());

        if (! in_array($ext, $allowed, true)) {
            return null;
        }

        if ($file->getSizeByUnit('kb') > $maxKb) {
            return null;
        }

        $newName = $file->getRandomName();
        $path    = FCPATH . 'uploads/' . trim($folder, '/');

        if (! is_dir($path)) {
            mkdir($path, 0755, true);
        }

        $file->move($path, $newName);

        return trim($folder, '/') . '/' . $newName;
    }
}
