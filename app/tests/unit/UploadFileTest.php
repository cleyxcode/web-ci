<?php

declare(strict_types=1);

use CodeIgniter\Test\CIUnitTestCase;

/**
 * UploadFileTest
 *
 * Menguji fungsi upload_file() di app_helper.php secara unit.
 *
 * Karena upload_file() bergantung pada objek UploadedFile CI4,
 * kita buat mock/stub sederhana yang mengimplementasikan interface
 * yang dibutuhkan.
 *
 * Skenario:
 *  - File null / tidak valid → return null
 *  - Ekstensi tidak diizinkan → return null
 *  - Ukuran melebihi batas → return null
 *  - File sudah dipindah → return null
 *  - File valid → return string path
 */
final class UploadFileTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        helper('app');
    }

    // ---------------------------------------------------------------
    // Stub helper
    // ---------------------------------------------------------------

    /**
     * Membuat anonymous object yang mensimulasikan UploadedFile CI4.
     */
    private function makeFileMock(
        bool $isValid,
        bool $hasMoved,
        string $extension,
        int $sizeKb,
        ?string $randomName = 'file_random.jpg'
    ): object {
        return new class($isValid, $hasMoved, $extension, $sizeKb, $randomName) {
            public function __construct(
                private bool $valid,
                private bool $moved,
                private string $ext,
                private int $sizeKb,
                private ?string $name
            ) {}

            public function isValid(): bool
            {
                return $this->valid;
            }

            public function hasMoved(): bool
            {
                return $this->moved;
            }

            public function getExtension(): string
            {
                return $this->ext;
            }

            public function getSizeByUnit(string $unit): int
            {
                return $unit === 'kb' ? $this->sizeKb : $this->sizeKb * 1024;
            }

            public function getRandomName(): string
            {
                return $this->name ?? 'random_' . time() . '.' . $this->ext;
            }

            public function move(string $path, string $name): bool
            {
                // Simulasi move berhasil (tidak benar-benar memindah file)
                return true;
            }
        };
    }

    // ---------------------------------------------------------------
    // Kasus GAGAL
    // ---------------------------------------------------------------

    public function testFileNullReturnNull(): void
    {
        $result = upload_file(null, 'logbook', ['jpg', 'jpeg', 'png']);

        $this->assertNull($result, 'File null seharusnya return null');
    }

    public function testFileIsValidFalseReturnNull(): void
    {
        $file = $this->makeFileMock(false, false, 'jpg', 100);

        $result = upload_file($file, 'logbook', ['jpg', 'jpeg', 'png']);

        $this->assertNull($result, 'File tidak valid (isValid=false) seharusnya return null');
    }

    public function testFileHasMovedReturnNull(): void
    {
        $file = $this->makeFileMock(true, true, 'jpg', 100);

        $result = upload_file($file, 'logbook', ['jpg', 'jpeg', 'png']);

        $this->assertNull($result, 'File yang sudah dipindah (hasMoved=true) seharusnya return null');
    }

    public function testEkstensiTidakDiizinkanReturnNull(): void
    {
        // Kirim file .exe ke endpoint yang hanya terima jpg/png
        $file = $this->makeFileMock(true, false, 'exe', 100);

        $result = upload_file($file, 'logbook', ['jpg', 'jpeg', 'png']);

        $this->assertNull($result, 'Ekstensi .exe tidak diizinkan seharusnya return null');
    }

    public function testEkstensiPhpTidakDiizinkanReturnNull(): void
    {
        $file = $this->makeFileMock(true, false, 'php', 50);

        $result = upload_file($file, 'laporan', ['pdf']);

        $this->assertNull($result, 'Ekstensi .php tidak diizinkan seharusnya return null');
    }

    public function testEkstensiPdfDitolakUntukLogbook(): void
    {
        // Logbook hanya terima jpg/jpeg/png — bukan pdf
        $file = $this->makeFileMock(true, false, 'pdf', 200);

        $result = upload_file($file, 'logbook', ['jpg', 'jpeg', 'png']);

        $this->assertNull($result, 'PDF tidak boleh diupload ke logbook (hanya jpg/png)');
    }

    public function testUkuranMelebihiBatasReturnNull(): void
    {
        // Default batas 5120 KB (5 MB), kirim 6000 KB
        $file = $this->makeFileMock(true, false, 'pdf', 6000);

        $result = upload_file($file, 'laporan', ['pdf'], 5120);

        $this->assertNull($result, 'File > 5MB seharusnya return null');
    }

    public function testUkuranTepat5MbDiterima(): void
    {
        // Tepat 5120 KB seharusnya masih diterima (batas inklusif ≤)
        $file = $this->makeFileMock(true, false, 'pdf', 5120);

        $result = upload_file($file, 'laporan', ['pdf'], 5120);

        $this->assertNotNull($result, 'File tepat 5MB seharusnya diterima');
        $this->assertStringContainsString('laporan/', $result);
    }

    public function testUkuranSatuKbLebihDariBatasDitolak(): void
    {
        $file = $this->makeFileMock(true, false, 'jpg', 5121);

        $result = upload_file($file, 'logbook', ['jpg', 'jpeg', 'png'], 5120);

        $this->assertNull($result, 'File 5121 KB (1 KB melebihi batas) seharusnya return null');
    }

    // ---------------------------------------------------------------
    // Kasus BERHASIL
    // ---------------------------------------------------------------

    public function testFileJpgValidReturnPath(): void
    {
        $file = $this->makeFileMock(true, false, 'jpg', 500, 'foto_kegiatan.jpg');

        $result = upload_file($file, 'logbook', ['jpg', 'jpeg', 'png']);

        $this->assertNotNull($result, 'File JPG valid seharusnya return string path');
        $this->assertStringStartsWith('logbook/', $result, 'Path harus diawali nama folder');
    }

    public function testFilePngValidReturnPath(): void
    {
        $file = $this->makeFileMock(true, false, 'png', 1024, 'dokumentasi.png');

        $result = upload_file($file, 'logbook', ['jpg', 'jpeg', 'png']);

        $this->assertNotNull($result);
        $this->assertStringStartsWith('logbook/', $result);
    }

    public function testFilePdfValidReturnPath(): void
    {
        $file = $this->makeFileMock(true, false, 'pdf', 2048, 'laporan_akhir.pdf');

        $result = upload_file($file, 'laporan', ['pdf']);

        $this->assertNotNull($result, 'File PDF valid seharusnya return string path');
        $this->assertStringStartsWith('laporan/', $result, 'Path harus diawali folder laporan');
    }

    public function testEkstensiCaseInsensitif(): void
    {
        // Ekstensi huruf besar seharusnya tetap diterima (karena di-strtolower)
        $file = $this->makeFileMock(true, false, 'JPG', 300, 'UPPER.JPG');

        // getExtension() mengembalikan 'JPG' — upload_file akan strtolower → 'jpg'
        $result = upload_file($file, 'logbook', ['jpg', 'jpeg', 'png']);

        $this->assertNotNull($result, 'Ekstensi huruf kapital seharusnya tetap diterima (case-insensitive)');
    }

    public function testFolderPathTersimpanDiReturnValue(): void
    {
        $file = $this->makeFileMock(true, false, 'jpg', 100, 'test.jpg');

        $result = upload_file($file, '/logbook/', ['jpg']);

        $this->assertNotNull($result);
        // trim('/') pada folder seharusnya membersihkan slash
        $this->assertStringStartsWith('logbook/', $result, 'Slash awal/akhir folder harus di-trim');
    }

    // ---------------------------------------------------------------
    // BUG: tidak ada validasi mime-type — hanya cek ekstensi
    // File berbahaya bisa rename extension untuk lolos
    // ---------------------------------------------------------------

    public function testBugHanyaCekEkstensiTanpaMimeType(): void
    {
        // File .php yang direname jadi .jpg akan lolos karena
        // upload_file() hanya cek getExtension(), bukan MIME type sebenarnya.
        // Test ini mendokumentasikan celah keamanan tersebut.
        $file = $this->makeFileMock(true, false, 'jpg', 100, 'malicious.jpg');

        // Dari sudut pandang upload_file(), file ini "valid" karena extension jpg
        $result = upload_file($file, 'logbook', ['jpg', 'jpeg', 'png']);

        // Ini lolos — mendokumentasikan bahwa validasi MIME type tidak dilakukan
        $this->assertNotNull(
            $result,
            'BUG DOKUMENTASI: File dengan extension .jpg lolos tanpa validasi MIME type sebenarnya'
        );
    }
}
