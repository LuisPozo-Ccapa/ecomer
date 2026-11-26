<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class RobotsController extends Controller
{
    /**
     * Genera dinámicamente el archivo robots.txt adaptado al dominio actual del tenant.
     */
    public function index(): Response
    {
        $scheme = (request()->secure() || request()->header('X-Forwarded-Proto') === 'https') ? 'https' : 'http';
        $host = request()->getHost();
        $port = request()->getPort();
        $portStr = ($port && ! in_array($port, [80, 443])) ? ":{$port}" : '';

        $baseUrl = "{$scheme}://{$host}{$portStr}";
        $sitemapUrl = "{$baseUrl}/sitemap.xml";

        $lines = [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /admin/',
            'Disallow: /livewire/',
            'Disallow: /api/deploy/',
            'Disallow: /storage/framework/',
            'Allow: /',
            '',
            "Sitemap: {$sitemapUrl}",
        ];

        return response(implode("\n", $lines), 200, [
            'Content-Type'  => 'text/plain; charset=utf-8',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
