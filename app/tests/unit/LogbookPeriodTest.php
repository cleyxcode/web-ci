<?php

use App\Models\LogbookModel;
use CodeIgniter\Test\CIUnitTestCase;

final class LogbookPeriodTest extends CIUnitTestCase
{
    public function testDashboardPeriodsResolveToExpectedDates(): void
    {
        $model = new LogbookModel();

        $day = $model->resolvePeriod('hari', '2026-08-16');
        $week = $model->resolvePeriod('minggu', '2026-08-16');

        $this->assertSame('2026-08-16', $day['start']);
        $this->assertSame('2026-08-16', $day['end']);
        $this->assertSame('2026-08-10', $week['start']);
        $this->assertSame('2026-08-16', $week['end']);
    }

    public function testModelDefaultPeriodKeepsExistingAllLogbookQueriesUnfiltered(): void
    {
        $model = new LogbookModel();

        $this->assertSame('semua', $model->normalizePeriod(null));
        $this->assertSame('hari', $model->normalizePeriod('hari'));
        $this->assertSame('minggu', $model->normalizePeriod('minggu'));
    }
}
