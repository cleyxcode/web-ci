<?php

declare(strict_types=1);

use CodeIgniter\Test\CIUnitTestCase;

final class SeoTest extends CIUnitTestCase
{
    public function testSeoConfigUsesProductionDomain(): void
    {
        $seo = config('Seo');

        $this->assertSame('https://slategray-skunk-297972.hostingersite.com', $seo->siteUrl);
        $this->assertStringContainsString('KKN Tematik UKIM', $seo->defaultDescription);
        $this->assertContains('monitoring KKN Tematik UKIM', $seo->keywords);
        $this->assertSame('', $seo->googleSiteVerification);
    }

    public function testPublicSeoFilesAreAvailable(): void
    {
        $robots = (string) file_get_contents(FCPATH . 'robots.txt');
        $sitemap = (string) file_get_contents(FCPATH . 'sitemap.xml');

        $this->assertStringContainsString('Sitemap: https://slategray-skunk-297972.hostingersite.com/sitemap.xml', $robots);
        $this->assertStringContainsString('<loc>https://slategray-skunk-297972.hostingersite.com/</loc>', $sitemap);
    }

    public function testRootRouteIsPublicLandingPage(): void
    {
        $routes = (string) file_get_contents(APPPATH . 'Config/Routes.php');

        $this->assertStringContainsString("\$routes->get('/', 'Home::index');", $routes);
        $this->assertFileExists(APPPATH . 'Views/public/home.php');

        $home = (string) file_get_contents(APPPATH . 'Views/public/home.php');
        $this->assertStringContainsString('meta name="keywords"', $home);
        $this->assertStringContainsString('google-site-verification', $home);
    }
}
