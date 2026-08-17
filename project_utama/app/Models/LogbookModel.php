<?php

namespace App\Models;

use CodeIgniter\Model;
use DateTimeImmutable;
use DateTimeZone;

class LogbookModel extends Model
{
    protected $table            = 'logbook';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'mahasiswa_id', 'tanggal', 'kegiatan', 'lokasi_kegiatan', 'dokumentasi',
        'status', 'catatan_dpl', 'validated_by', 'validated_at',
    ];
    protected $useTimestamps = false;

    public function getByMahasiswa(int $mahasiswaId, ?string $period = null, ?string $anchorDate = null): array
    {
        $builder = $this->where('mahasiswa_id', $mahasiswaId);
        $this->applyPeriodFilter($builder, $period, $anchorDate);

        return $builder->orderBy('tanggal', 'DESC')->findAll();
    }

    public function getPendingByDpl(int $dplId): array
    {
        return $this->getByDpl($dplId, 'menunggu', 'ASC');
    }

    public function getByDpl(
        int $dplId,
        ?string $status = null,
        string $direction = 'DESC',
        ?string $period = null,
        ?string $anchorDate = null
    ): array
    {
        $builder = $this->select('logbook.*, mahasiswa.nama as nama_mahasiswa, mahasiswa.npm,
                kelompok_kkn.nama_kelompok, kelompok_kkn.periode,
                lokasi_kkn.nama_desa, lokasi_kkn.kecamatan, lokasi_kkn.kabupaten')
            ->join('mahasiswa', 'mahasiswa.id = logbook.mahasiswa_id')
            ->join('kelompok_kkn', 'kelompok_kkn.id = mahasiswa.kelompok_id')
            ->join('lokasi_kkn', 'lokasi_kkn.id = kelompok_kkn.lokasi_id', 'left')
            ->where('kelompok_kkn.dpl_id', $dplId);

        if ($status !== null) {
            $builder->where('logbook.status', $status);
        }

        $this->applyPeriodFilter($builder, $period, $anchorDate, 'logbook.tanggal');

        return $builder
            ->orderBy('logbook.created_at', strtoupper($direction) === 'ASC' ? 'ASC' : 'DESC')
            ->findAll();
    }

    public function countByMahasiswaStatus(int $mahasiswaId, string $status, ?string $period = null, ?string $anchorDate = null): int
    {
        $builder = $this->where('mahasiswa_id', $mahasiswaId)->where('status', $status);
        $this->applyPeriodFilter($builder, $period, $anchorDate);

        return $builder->countAllResults();
    }

    public function countByMahasiswaPeriod(int $mahasiswaId, ?string $period = null, ?string $anchorDate = null): int
    {
        $builder = $this->where('mahasiswa_id', $mahasiswaId);
        $this->applyPeriodFilter($builder, $period, $anchorDate);

        return $builder->countAllResults();
    }

    public function countByPeriod(?string $period = null, ?string $anchorDate = null): int
    {
        $builder = $this->builder();
        $this->applyPeriodFilter($builder, $period, $anchorDate);

        return $builder->countAllResults();
    }

    public function normalizePeriod(?string $period): string
    {
        return in_array($period, ['hari', 'minggu', 'semua'], true) ? $period : 'semua';
    }

    public function normalizeAnchorDate(?string $anchorDate): string
    {
        $timezone = new DateTimeZone(config('App')->appTimezone);
        $today = new DateTimeImmutable('now', $timezone);

        if ($anchorDate === null || $anchorDate === '') {
            return $today->format('Y-m-d');
        }

        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $anchorDate, $timezone);

        return $date !== false && $date->format('Y-m-d') === $anchorDate
            ? $anchorDate
            : $today->format('Y-m-d');
    }

    public function resolvePeriod(?string $period = null, ?string $anchorDate = null): array
    {
        $period = $this->normalizePeriod($period);
        $anchorDate = $this->normalizeAnchorDate($anchorDate);

        if ($period === 'semua') {
            return [
                'period' => $period,
                'date'   => $anchorDate,
                'start'  => null,
                'end'    => null,
            ];
        }

        $timezone = new DateTimeZone(config('App')->appTimezone);
        $anchor = new DateTimeImmutable($anchorDate, $timezone);
        $start = $period === 'hari' ? $anchor : $anchor->modify('monday this week');
        $end = $period === 'hari' ? $start : $start->modify('+6 days');

        return [
            'period' => $period,
            'date'   => $anchorDate,
            'start'  => $start->format('Y-m-d'),
            'end'    => $end->format('Y-m-d'),
        ];
    }

    public function getDashboardSeries(?string $period = null, ?string $anchorDate = null): array
    {
        $period = $this->normalizePeriod($period);

        if ($period === 'semua') {
            return $this->select("DATE_FORMAT(tanggal, '%Y-%u') as label, COUNT(*) as total")
                ->groupBy('label')
                ->orderBy('label', 'ASC')
                ->findAll(8);
        }

        $range = $this->resolvePeriod($period, $anchorDate);
        $rows = $this->select("DATE_FORMAT(tanggal, '%Y-%m-%d') as label, COUNT(*) as total")
            ->where('tanggal >=', $range['start'])
            ->where('tanggal <=', $range['end'])
            ->groupBy('label')
            ->orderBy('label', 'ASC')
            ->findAll();
        $totals = [];

        foreach ($rows as $row) {
            $totals[$row['label']] = (int) $row['total'];
        }

        if ($period === 'hari') {
            return [[
                'label' => $range['start'],
                'total' => $totals[$range['start']] ?? 0,
            ]];
        }

        $timezone = new DateTimeZone(config('App')->appTimezone);
        $day = new DateTimeImmutable($range['start'], $timezone);
        $series = [];

        for ($index = 0; $index < 7; $index++) {
            $label = $day->modify('+' . $index . ' days')->format('Y-m-d');
            $series[] = [
                'label' => $label,
                'total' => $totals[$label] ?? 0,
            ];
        }

        return $series;
    }

    private function applyPeriodFilter($builder, ?string $period, ?string $anchorDate, string $column = 'tanggal'): void
    {
        $range = $this->resolvePeriod($period, $anchorDate);

        if ($range['start'] !== null) {
            $builder->where($column . ' >=', $range['start'])
                ->where($column . ' <=', $range['end']);
        }
    }
}
