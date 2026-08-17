<?php

namespace App\Libraries;

class ExportLib
{
    /**
     * @param list<string> $headers
     * @param list<list<string|int|float|null>> $rows
     */
    public static function download(string $basename, array $headers, array $rows, ?string $format = null)
    {
        $format = strtolower((string) ($format ?: service('request')->getGet('format') ?: 'xls'));

        if ($format === 'csv') {
            return self::csv($basename . '.csv', $headers, $rows);
        }

        return self::xls($basename . '.xls', $headers, $rows);
    }

    /**
     * @param list<string> $headers
     * @param list<list<string|int|float|null>> $rows
     */
    public static function csv(string $filename, array $headers, array $rows)
    {
        $response = service('response');
        $response->setHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->setHeader('Pragma', 'no-cache');
        $response->setHeader('Expires', '0');

        $out = fopen('php://temp', 'r+');
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, $headers);

        foreach ($rows as $row) {
            fputcsv($out, $row);
        }

        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);

        return $response->setBody($csv ?: '');
    }

    /**
     * SpreadsheetML (.xls) — terbuka langsung di Excel / LibreOffice.
     *
     * @param list<string> $headers
     * @param list<list<string|int|float|null>> $rows
     */
    public static function xls(string $filename, array $headers, array $rows)
    {
        $response = service('response');
        $response->setHeader('Content-Type', 'application/vnd.ms-excel; charset=UTF-8');
        $response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->setHeader('Pragma', 'no-cache');
        $response->setHeader('Expires', '0');

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">';
        $xml .= '<Worksheet ss:Name="Data"><Table>';
        $xml .= self::xlsRow($headers);

        foreach ($rows as $row) {
            $xml .= self::xlsRow($row);
        }

        $xml .= '</Table></Worksheet></Workbook>';

        return $response->setBody($xml);
    }

    /**
     * @param list<string|int|float|null> $cells
     */
    private static function xlsRow(array $cells): string
    {
        $xml = '<Row>';

        foreach ($cells as $cell) {
            $value = htmlspecialchars((string) ($cell ?? ''), ENT_XML1 | ENT_QUOTES, 'UTF-8');
            $xml  .= '<Cell><Data ss:Type="String">' . $value . '</Data></Cell>';
        }

        return $xml . '</Row>';
    }
}
