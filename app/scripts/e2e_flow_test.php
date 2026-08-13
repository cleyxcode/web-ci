#!/usr/bin/env php
<?php

/**
 * E2E flow tester — semua role (kecuali reset password).
 * Usage: php scripts/e2e_flow_test.php
 */

declare(strict_types=1);

$base = getenv('KKN_BASE') ?: 'http://localhost:8083';
$cookieDir = sys_get_temp_dir() . '/kkn_e2e_' . getmypid();
@mkdir($cookieDir, 0700, true);

$pass = 0;
$fail = 0;
$bugs = [];

function out(string $msg): void
{
    echo $msg . PHP_EOL;
}

function ok(bool $cond, string $label, string $detail = ''): void
{
    global $pass, $fail, $bugs;
    if ($cond) {
        $pass++;
        out("  ✓ {$label}");
    } else {
        $fail++;
        $bugs[] = $label . ($detail !== '' ? " — {$detail}" : '');
        out("  ✗ {$label}" . ($detail !== '' ? " | {$detail}" : ''));
    }
}

function jar(string $role): string
{
    global $cookieDir;

    return $cookieDir . "/{$role}.jar";
}

function http(string $method, string $url, array $opts = []): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => true,
        CURLOPT_CUSTOMREQUEST  => strtoupper($method),
        CURLOPT_FOLLOWLOCATION => $opts['follow'] ?? false,
        CURLOPT_MAXREDIRS      => 5,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_HTTPHEADER     => $opts['headers'] ?? [],
        CURLOPT_COOKIEJAR      => $opts['cookie'] ?? null,
        CURLOPT_COOKIEFILE     => $opts['cookie'] ?? null,
    ]);

    if (array_key_exists('fields', $opts)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, ! empty($opts['multipart'])
            ? $opts['fields']
            : http_build_query($opts['fields']));
    }

    $raw  = curl_exec($ch);
    $err  = curl_error($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $hs   = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    curl_close($ch);

    if ($raw === false) {
        return ['code' => 0, 'headers' => '', 'body' => '', 'error' => $err];
    }

    return [
        'code'    => $code,
        'headers' => substr($raw, 0, $hs),
        'body'    => substr($raw, $hs),
        'error'   => '',
    ];
}

function extractCsrf(string $html): array
{
    if (preg_match('/name="(csrf_[^"]+)"\s+value="([^"]+)"/i', $html, $m)) {
        return [$m[1], $m[2]];
    }
    if (preg_match('/name="([^"]*csrf[^"]*)"[^>]*value="([^"]+)"/i', $html, $m)) {
        return [$m[1], $m[2]];
    }

    return ['csrf_test_name', ''];
}

function login(string $role, string $user, string $password): bool
{
    global $base;
    $cookie = jar($role);
    @unlink($cookie);

    $page = http('GET', $base . '/login', ['cookie' => $cookie]);
    ok($page['code'] === 200, "[{$role}] GET /login", (string) $page['code']);

    [$csrfName, $csrfHash] = extractCsrf($page['body']);
    ok($csrfHash !== '', "[{$role}] CSRF login tersedia");

    $res = http('POST', $base . '/login', [
        'cookie' => $cookie,
        'fields' => [
            $csrfName  => $csrfHash,
            'login'    => $user,
            'password' => $password,
        ],
    ]);

    $ok = in_array($res['code'], [302, 303], true);
    ok($ok, "[{$role}] POST login {$user}", 'HTTP ' . $res['code']);

    return $ok;
}

function getAuthed(string $role, string $path): array
{
    global $base;

    return http('GET', $base . $path, ['cookie' => jar($role), 'follow' => true]);
}

function postFromPage(string $role, string $formPage, string $actionPath, array $fields): array
{
    global $base;
    $page = getAuthed($role, $formPage);
    [$csrfName, $csrfHash] = extractCsrf($page['body']);
    $fields[$csrfName] = $csrfHash;

    return http('POST', $base . $actionPath, [
        'cookie' => jar($role),
        'fields' => $fields,
    ]);
}

out("=== KKN E2E Flow Test @ {$base} ===\n");

out('## Auth / Login');
login('admin', 'admin', 'admin123');
login('dpl', 'dpl1', 'admin123');
login('mhs_ketua', '12155201220035', 'mahasiswa123');
login('mhs_anggota', '12155201220036', 'mahasiswa123');

out("\n## Admin pages");
foreach ([
    '/admin/dashboard', '/admin/mahasiswa', '/admin/dpl', '/admin/kkn', '/admin/lokasi',
    '/admin/laporan', '/admin/analitik', '/admin/export', '/admin/audit',
    '/admin/pengumuman', '/admin/profil', '/notifikasi',
] as $p) {
    $r = getAuthed('admin', $p);
    ok($r['code'] === 200 && ! str_contains($r['body'], 'Whoops!'), "Admin GET {$p}", 'HTTP ' . $r['code']);
}

out("\n## Admin: set ketua");
$r = postFromPage('admin', '/admin/kkn/1', '/admin/kkn/1/ketua', ['ketua_mahasiswa_id' => '1']);
ok(in_array($r['code'], [302, 303], true), 'Admin set ketua → redirect', 'HTTP ' . $r['code']);
$show = getAuthed('admin', '/admin/kkn/1');
ok(str_contains($show['body'], 'Clara') || str_contains($show['body'], 'Ketua'), 'Detail kelompok tampil ketua');

out("\n## Admin: lokasi");
$r = postFromPage('admin', '/admin/lokasi/create', '/admin/lokasi', [
    'nama_desa' => 'Desa Uji E2E',
    'kecamatan' => 'Kec Uji',
    'kabupaten' => 'Kab Uji',
]);
ok(in_array($r['code'], [302, 303], true), 'Admin create lokasi', 'HTTP ' . $r['code']);
ok(str_contains(getAuthed('admin', '/admin/lokasi')['body'], 'Desa Uji E2E'), 'Lokasi baru di list');

out("\n## Admin: pengumuman → notifikasi");
$r = postFromPage('admin', '/admin/pengumuman/create', '/admin/pengumuman', [
    'judul' => 'Pengumuman E2E',
    'isi'   => 'Isi pengumuman untuk uji notifikasi sistem.',
]);
ok(in_array($r['code'], [302, 303], true), 'Admin buat pengumuman', 'HTTP ' . $r['code']);
ok(str_contains(getAuthed('mhs_ketua', '/notifikasi')['body'], 'Pengumuman E2E'), 'Mahasiswa terima notif pengumuman');

out("\n## Admin: export");
foreach (['mahasiswa', 'logbook', 'laporan', 'nilai', 'kelompok'] as $ex) {
    $r = http('GET', $base . '/admin/export/' . $ex, ['cookie' => jar('admin')]);
    ok($r['code'] === 200 && strlen($r['body']) > 10, "Admin export {$ex} xls", 'HTTP ' . $r['code']);
    $r2 = http('GET', $base . '/admin/export/' . $ex . '?format=csv', ['cookie' => jar('admin')]);
    ok($r2['code'] === 200 && strlen($r2['body']) > 5, "Admin export {$ex} csv", 'HTTP ' . $r2['code']);
}

out("\n## Mahasiswa ketua: GPS");
$tim = getAuthed('mhs_ketua', '/mahasiswa/tim');
ok($tim['code'] === 200, 'Ketua GET /mahasiswa/tim');
ok(str_contains($tim['body'], 'gps-lat') || str_contains($tim['body'], 'name="latitude"'), 'Form GPS ketua tampil');

$r = postFromPage('mhs_ketua', '/mahasiswa/tim', '/mahasiswa/tim/gps', [
    'latitude'  => '-3.6951234',
    'longitude' => '128.1834567',
]);
ok(in_array($r['code'], [302, 303], true), 'Ketua simpan GPS', 'HTTP ' . $r['code']);

$tim2 = getAuthed('mhs_ketua', '/mahasiswa/tim');
ok(
    str_contains($tim2['body'], '-3.6951234') || str_contains($tim2['body'], 'data-map'),
    'Peta/koordinat tampil setelah GPS'
);

$timAnggota = getAuthed('mhs_anggota', '/mahasiswa/tim');
ok(
    (str_contains($timAnggota['body'], '-3.695') || str_contains($timAnggota['body'], 'data-map'))
    && ! str_contains($timAnggota['body'], 'name="latitude"'),
    'Anggota lihat peta, tidak bisa edit GPS'
);

postFromPage('mhs_anggota', '/mahasiswa/tim', '/mahasiswa/tim/gps', [
    'latitude'  => '-1.1111111',
    'longitude' => '100.1111111',
]);
ok(! str_contains(getAuthed('mhs_anggota', '/mahasiswa/tim')['body'], '-1.1111111'), 'Anggota tidak bisa overwrite GPS');

postFromPage('mhs_ketua', '/mahasiswa/tim', '/mahasiswa/tim/gps', [
    'latitude'  => '999',
    'longitude' => '128',
]);
// validasi harus menolak — koordinat tetap yang lama
ok(
    ! str_contains(getAuthed('mhs_ketua', '/mahasiswa/tim')['body'], '999'),
    'GPS di luar rentang ditolak'
);

out("\n## Mahasiswa: logbook & laporan");
$r = postFromPage('mhs_ketua', '/mahasiswa/logbook/create', '/mahasiswa/logbook', [
    'tanggal'         => date('Y-m-d'),
    'kegiatan'        => 'Survey lokasi E2E ' . date('H:i:s'),
    'lokasi_kegiatan' => 'Balai Desa Waai',
]);
ok(in_array($r['code'], [302, 303], true), 'Submit logbook', 'HTTP ' . $r['code']);
ok(str_contains(getAuthed('mhs_ketua', '/mahasiswa/logbook')['body'], 'Survey lokasi E2E'), 'Logbook di list');

$pdfPath = $cookieDir . '/laporan_e2e.pdf';
file_put_contents($pdfPath, "%PDF-1.4\n1 0 obj<<>>endobj\ntrailer<<>>\n%%EOF\n");
$page = getAuthed('mhs_ketua', '/mahasiswa/laporan/create');
[$csrfName, $csrfHash] = extractCsrf($page['body']);
$r = http('POST', $base . '/mahasiswa/laporan', [
    'cookie'    => jar('mhs_ketua'),
    'multipart' => true,
    'fields'    => [
        $csrfName      => $csrfHash,
        'judul'        => 'Laporan E2E ' . date('His'),
        'deskripsi'    => 'Deskripsi laporan uji e2e',
        'file_laporan' => new CURLFile($pdfPath, 'application/pdf', 'laporan_e2e.pdf'),
    ],
]);
ok(in_array($r['code'], [302, 303], true), 'Upload laporan PDF', 'HTTP ' . $r['code'] . ' ' . substr(strip_tags($r['body']), 0, 80));
ok(str_contains(getAuthed('mhs_ketua', '/mahasiswa/laporan')['body'], 'Laporan E2E'), 'Laporan di list');

$dashM = getAuthed('mhs_ketua', '/mahasiswa/dashboard');
ok($dashM['code'] === 200 && (str_contains($dashM['body'], 'data-map') || str_contains($dashM['body'], 'Lokasi')), 'Dashboard mhs tampil GPS');

out("\n## DPL pages");
foreach (['/dpl/dashboard', '/dpl/monitoring', '/dpl/logbook', '/dpl/laporan', '/dpl/penilaian', '/dpl/export'] as $p) {
    $r = getAuthed('dpl', $p);
    ok($r['code'] === 200 && ! str_contains($r['body'], 'Whoops!'), "DPL GET {$p}", 'HTTP ' . $r['code']);
}
ok(
    str_contains(getAuthed('dpl', '/dpl/dashboard')['body'], 'data-map')
    || str_contains(getAuthed('dpl', '/dpl/dashboard')['body'], 'Peta'),
    'DPL dashboard peta GPS'
);
ok(
    str_contains(getAuthed('dpl', '/notifikasi')['body'], 'Logbook')
    || str_contains(getAuthed('dpl', '/notifikasi')['body'], 'logbook')
    || str_contains(getAuthed('dpl', '/notifikasi')['body'], 'menunggu'),
    'DPL dapat notifikasi logbook'
);

$logPage = getAuthed('dpl', '/dpl/logbook');
if (preg_match('#dpl/logbook/(\d+)/proses#', $logPage['body'], $m)) {
    $logId = $m[1];
    $r = postFromPage('dpl', '/dpl/logbook', '/dpl/logbook/' . $logId . '/proses', [
        'action'      => 'validasi',
        'catatan_dpl' => 'OK E2E',
    ]);
    ok(in_array($r['code'], [302, 303], true), "DPL validasi logbook #{$logId}", 'HTTP ' . $r['code']);
    $notifM = getAuthed('mhs_ketua', '/notifikasi');
    ok(
        str_contains($notifM['body'], 'Divalidasi')
        || str_contains($notifM['body'], 'divalidasi')
        || str_contains($notifM['body'], 'Logbook'),
        'Mahasiswa notif logbook divalidasi'
    );
} else {
    ok(false, 'DPL form validasi logbook ditemukan');
}

$lapPage = getAuthed('dpl', '/dpl/laporan');
if (preg_match('#dpl/laporan/(\d+)/review#', $lapPage['body'], $m)) {
    $lapId = $m[1];
    $r = postFromPage('dpl', '/dpl/laporan', '/dpl/laporan/' . $lapId . '/review', [
        'action'      => 'terima',
        'catatan_dpl' => 'Diterima E2E',
    ]);
    ok(in_array($r['code'], [302, 303], true), "DPL terima laporan #{$lapId}", 'HTTP ' . $r['code']);
} else {
    ok(false, 'DPL form review laporan ditemukan');
}

out("\n## DPL: penilaian + KNN");
$form = getAuthed('dpl', '/dpl/penilaian/1');
ok($form['code'] === 200 && str_contains($form['body'], 'Prediksi KNN'), 'Form penilaian + prediksi KNN');
$r = postFromPage('dpl', '/dpl/penilaian/1', '/dpl/penilaian/1', [
    'nilai_keaktifan' => '85',
    'nilai_logbook'   => '80',
    'nilai_laporan'   => '90',
    'grade'           => '',
    'prediksi_knn'    => 'A',
    'catatan'         => 'Nilai E2E',
]);
ok(in_array($r['code'], [302, 303], true), 'DPL simpan penilaian', 'HTTP ' . $r['code']);
$nilai = getAuthed('mhs_ketua', '/mahasiswa/nilai');
ok(
    str_contains($nilai['body'], '85')
    || str_contains($nilai['body'], '80')
    || str_contains($nilai['body'], 'Grade')
    || preg_match('/\bA\b/', $nilai['body']) === 1,
    'Mahasiswa lihat nilai'
);

foreach (['logbook', 'laporan', 'nilai'] as $ex) {
    $r = http('GET', $base . '/dpl/export/' . $ex, ['cookie' => jar('dpl')]);
    ok($r['code'] === 200 && strlen($r['body']) > 5, "DPL export {$ex}", 'HTTP ' . $r['code']);
}

out("\n## Admin: analitik & audit");
$an = getAuthed('admin', '/admin/analitik');
ok($an['code'] === 200 && (str_contains($an['body'], 'KNN') || str_contains($an['body'], 'chartGrade')), 'Analitik render OK');
ok(str_contains($an['body'], 'data-map') || str_contains($an['body'], 'GPS'), 'Analitik peta GPS');

$audit = getAuthed('admin', '/admin/audit');
ok($audit['code'] === 200, 'Audit page OK');
ok(
    str_contains($audit['body'], 'divalidasi')
    || str_contains($audit['body'], 'set_gps')
    || str_contains($audit['body'], 'set_ketua')
    || str_contains($audit['body'], 'publish_nilai')
    || str_contains($audit['body'], 'export')
    || str_contains($audit['body'], 'diterima'),
    'Audit trail berisi aksi sistem'
);

$ad = getAuthed('admin', '/admin/dashboard');
ok(str_contains($ad['body'], 'data-map') || str_contains($ad['body'], 'Peta lokasi'), 'Admin dashboard peta GPS');

out("\n## Isolasi role");
$r = getAuthed('mhs_ketua', '/admin/dashboard');
ok(
    $r['code'] === 200 && (str_contains(strtolower($r['body']), 'login') || ! str_contains($r['body'], 'Mahasiswa terbaru')),
    'Mahasiswa tidak akses admin',
    'HTTP ' . $r['code']
);
$r = getAuthed('dpl', '/admin/export');
ok(
    ! str_contains($r['body'], 'Data mahasiswa') || str_contains(strtolower($r['body']), 'login'),
    'DPL tidak akses admin export',
    'HTTP ' . $r['code']
);

out("\n## Logout");
$r = http('GET', $base . '/logout', ['cookie' => jar('admin')]);
ok(in_array($r['code'], [302, 303], true), 'Admin logout');

out("\n=== HASIL ===");
out("PASS: {$pass}");
out("FAIL: {$fail}");
if ($bugs !== []) {
    out("\nBug / gagal:");
    foreach ($bugs as $b) {
        out(' - ' . $b);
    }
}

foreach (glob($cookieDir . '/*') ?: [] as $f) {
    @unlink($f);
}
@rmdir($cookieDir);

exit($fail > 0 ? 1 : 0);
