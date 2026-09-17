<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Seo extends BaseConfig
{
    public string $siteName = 'KKN Tematik UKIM';
    public string $siteUrl = 'https://kkntematikukim.site';
    public string $defaultDescription = 'Monitoring KKN Tematik UKIM untuk mengelola kegiatan lapangan, logbook KKN, laporan, GPS tim, evaluasi, dan penilaian mahasiswa.';
    public array $keywords = [
        'KKN Tematik UKIM',
        'monitoring KKN Tematik UKIM',
        'sistem monitoring KKN',
        'monitoring kegiatan KKN mahasiswa',
        'logbook KKN',
        'laporan KKN',
        'DPL KKN',
        'penilaian KKN mahasiswa',
        'GPS lokasi KKN',
    ];
    public string $googleSiteVerification = 'ZisLNMIUr6B58UxNgfyKBnA4KUHayB6exKHeqvQetsw';
    public string $locale = 'id_ID';
}
