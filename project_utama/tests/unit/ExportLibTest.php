<?php

declare(strict_types=1);

use App\Libraries\ExportLib;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class ExportLibTest extends CIUnitTestCase
{
    public function testCsvContainsBomHeadersAndRows(): void
    {
        $response = ExportLib::csv('uji.csv', ['NPM', 'Nama'], [
            ['12155', 'Clara'],
            ['12156', 'Rian'],
        ]);

        $body = (string) $response->getBody();
        $this->assertStringStartsWith("\xEF\xBB\xBF", $body);
        $this->assertStringContainsString('NPM,Nama', $body);
        $this->assertStringContainsString('Clara', $body);
        $this->assertStringContainsString('attachment; filename="uji.csv"', $response->getHeaderLine('Content-Disposition'));
    }

    public function testXlsIsSpreadsheetMl(): void
    {
        $response = ExportLib::xls('uji.xls', ['A', 'B'], [['1', '2']]);
        $body     = (string) $response->getBody();

        $this->assertStringContainsString('Workbook', $body);
        $this->assertStringContainsString('<Data ss:Type="String">A</Data>', $body);
        $this->assertStringContainsString('application/vnd.ms-excel', $response->getHeaderLine('Content-Type'));
    }

    public function testDownloadDefaultsToXls(): void
    {
        $response = ExportLib::download('data', ['X'], [['y']], 'xls');
        $this->assertStringContainsString('.xls', $response->getHeaderLine('Content-Disposition'));
    }

    public function testDownloadCsvFormat(): void
    {
        $response = ExportLib::download('data', ['X'], [['y']], 'csv');
        $this->assertStringContainsString('.csv', $response->getHeaderLine('Content-Disposition'));
    }
}
