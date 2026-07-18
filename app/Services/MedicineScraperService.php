<?php

namespace App\Services;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class MedicineScraperService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.5',
            ],
            'timeout' => 20,
            'verify' => false,
        ]);
    }

    public function searchAndImport($drugName)
    {
        try {
            // 1. Search for the drug on K24Klik
            $searchUrl = "https://www.k24klik.com/cariObat/" . urlencode($drugName);
            $response = $this->client->get($searchUrl);
            $html = (string) $response->getBody();
            $crawler = new Crawler($html);

            // Find the first product link (K24Klik uses onclick for product details)
            $firstResult = $crawler->filter('a[onclick*="/p/"]')->first();
            if (!$firstResult->count()) {
                // Try fallback selector href
                $firstResult = $crawler->filter('a[href*="/p/"]')->first();
                if (!$firstResult->count()) return null;
            }

            $onclickAttr = $firstResult->attr('onclick') ?? '';
            preg_match("/'([^']+k24klik\.com\/p\/[^']+)'/", $onclickAttr, $matches);
            
            $detailUrl = $matches[1] ?? $firstResult->attr('href');

            if (!$detailUrl) return null;

            if (!str_starts_with($detailUrl, 'http')) {
                $detailUrl = "https://www.k24klik.com" . $detailUrl;
            }
            
            // 2. Get details
            $response = $this->client->get($detailUrl);
            $html = (string) $response->getBody();
            $crawler = new Crawler($html);

            $data = [];
            $data['name'] = $crawler->filter('h1[itemprop="name"]')->count() ? $crawler->filter('h1[itemprop="name"]')->text() : $drugName;
            
            // Classification detection logic
            $classificationSection = strtolower($this->extractSection($crawler, 'Golongan Obat'));
            
            // Try image-based detection (quick and reliable on K24Klik)
            $thumbType = $crawler->filter('.k24-thumb-type')->count() ? strtolower($crawler->filter('.k24-thumb-type')->attr('alt')) : '';
            
            if (str_contains($classificationSection, 'keras') || str_contains($classificationSection, 'resep dokter') || str_contains($thumbType, 'keras')) {
                $data['classification'] = 'keras';
            } elseif (str_contains($classificationSection, 'terbatas') || str_contains($thumbType, 'terbatas')) {
                $data['classification'] = 'terbatas';
            } else {
                $data['classification'] = 'bebas';
            }

            // Category Detection
            $nameLower = strtolower($data['name']);
            if (str_contains($nameLower, 'vitamin') || str_contains($nameLower, 'vit ')) {
                $data['category'] = 'vitamin';
            } elseif (str_contains($nameLower, 'suplemen') || str_contains($classificationSection, 'suplemen')) {
                $data['category'] = 'suplemen';
            } elseif (str_contains($nameLower, 'herbal') || str_contains($nameLower, 'jamu') || str_contains($classificationSection, 'herbal')) {
                $data['category'] = 'herbal';
            } else {
                $data['category'] = 'obat';
            }

            // Extract Description and Dosage
            $data['description'] = $this->extractSection($crawler, 'Deskripsi');
            $aturanPakaiText = strtolower($this->extractSection($crawler, 'Aturan Pakai'));
            $data['dosage'] = $this->extractSection($crawler, 'Dosis') . "\n\n" . $aturanPakaiText;
            $data['dosage'] = trim($data['dosage']);

            // Parse Aturan Pakai into structured field
            if (str_contains($aturanPakaiText, 'sesudah makan') || str_contains($aturanPakaiText, 'setelah makan')) {
                $data['aturan_pakai'] = 'sesudah_makan';
            } elseif (str_contains($aturanPakaiText, 'sebelum makan')) {
                $data['aturan_pakai'] = 'sebelum_makan';
            } elseif (str_contains($aturanPakaiText, 'bersama makan') || str_contains($aturanPakaiText, 'saat makan')) {
                $data['aturan_pakai'] = 'saat_makan';
            } else {
                $data['aturan_pakai'] = 'bebas';
            }

            // Add warning for Obat Keras
            if ($data['classification'] === 'keras') {
                $data['dosage'] = "⚠️ WAJIB DENGAN RESEP DOKTER.\n\n" . $data['dosage'];
            }

            // Get Image
            $imageUrl = $crawler->filter('img[itemprop="image"]')->count() ? $crawler->filter('img[itemprop="image"]')->attr('src') : null;
            if (!$imageUrl) {
                $imageUrl = $crawler->filter('.product-image img')->count() ? $crawler->filter('.product-image img')->attr('src') : null;
            }

            if ($imageUrl) {
                $data['image'] = $this->downloadImage($imageUrl, $data['name']);
            }

            return $data;

        } catch (Exception $e) {
            return null;
        }
    }

    protected function extractSection(Crawler $crawler, $keyword)
    {
        try {
            // Find the label specifically
            $nodes = $crawler->filter('h2, div.product-detail-title, b')->reduce(function (Crawler $node) use ($keyword) {
                return str_contains($node->text(), $keyword);
            });

            if ($nodes->count()) {
                $node = $nodes->first();
                
                // Get the next sibling text if it exists
                $next = $node->nextAll()->count() ? $node->nextAll()->first() : null;
                if ($next && trim($next->text()) !== '') {
                    return trim($next->text());
                }

                // If not in siblings, check parent's text but ONLY the part after the keyword
                $parentText = $node->parent()->text();
                $parts = explode($keyword, $parentText);
                if (count($parts) > 1) {
                    // Get only the immediate text after the keyword, limited to next line or similar
                    return trim(explode("\n", $parts[1])[0]);
                }
            }
            
            return '';
        } catch (Exception $e) {
            return '';
        }
    }

    protected function downloadImage($url, $name)
    {
        try {
            $response = $this->client->get($url);
            $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'png';
            $filename = 'obat-images/' . Str::slug($name) . '-' . time() . '.' . $extension;
            Storage::disk('public')->put($filename, $response->getBody());
            return $filename;
        } catch (Exception $e) {
            return null;
        }
    }
}
