<?php

namespace App\Controllers\Site;

use App\Core\Cache;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\SectionModel;
use App\Models\ServiceModel;
use App\Models\PackageModel;
use App\Models\PortfolioModel;
use App\Models\AdvantageModel;
use App\Models\ProcessModel;
use App\Models\StatModel;
use App\Models\FaqModel;
use App\Models\ArticleModel;
use App\Models\SettingModel;
use App\Services\PricingService;

class HomeController
{
    public function index(Request $request): void
    {
        // 1. Check HTML Page Cache if non-logged user
        $cacheKey = 'public_landing_page';
        $cachedHtml = Cache::get($cacheKey, 3600);
        if ($cachedHtml !== null && !\App\Core\Auth::check()) {
            Response::html($cachedHtml);
            return;
        }

        // 2. Load System Settings & Pricing
        $settings = SettingModel::getAll();
        if (empty($settings['meta_title']) || $settings['meta_title'] === 'Jasa Arsitek & Kontraktor Rumah Bogor | RBK Studio & RBK Konstruksi') {
            $settings['meta_title'] = 'Jasa Desain Arsitek & Kontraktor Rumah Bogor, Jasa Arsitek Jabodetabek | RBK Studio';
        }

        $pricingDataJson = json_encode(PricingService::getPricingJsonData(), JSON_UNESCAPED_UNICODE);

        // 3. Load Sections
        $sections = SectionModel::getVisibleSections();

        // 4. Load Data Lists
        $services     = ServiceModel::getPublishedServices();
        $packages     = PackageModel::getPublishedPackages();
        $categories   = PortfolioModel::getCategories();
        $portfolios   = PortfolioModel::getPublishedPortfolios();
        $advantages   = AdvantageModel::getPublished();
        $processSteps = ProcessModel::getPublished();
        $testimonials = [];
        $stats        = StatModel::getPublished();
        $faqs         = FaqModel::getPublished();
        $articles     = ArticleModel::getPublished();

        // Auto hide Testimonials section if present
        if (isset($sections['s14_testimonials'])) {
            unset($sections['s14_testimonials']);
        }

        // 5. Replace Placeholders & Format Asterisk Highlights in Section Titles
        foreach ($sections as $k => $sec) {
            if (isset($sec['title'])) {
                $sections[$k]['title'] = PricingService::formatTitle($sec['title']);
            }
        }

        // 6. JSON-LD Schemas (Section 10.1)
        $jsonLdSchemas = self::buildJsonLdSchemas($settings, $faqs);

        // 7. Render Landing Page View
        $html = View::render('public/home', [
            'settings'        => $settings,
            'pricingDataJson' => $pricingDataJson,
            'sections'        => $sections,
            'services'        => $services,
            'packages'        => $packages,
            'categories'      => $categories,
            'portfolios'      => $portfolios,
            'advantages'      => $advantages,
            'processSteps'    => $processSteps,
            'testimonials'    => $testimonials,
            'stats'           => $stats,
            'faqs'            => $faqs,
            'articles'        => $articles,
            'jsonLdSchemas'   => $jsonLdSchemas,
        ]);

        // 8. Cache HTML if not in debug mode
        if (!env('APP_DEBUG', false)) {
            Cache::set($cacheKey, $html);
        }

        Response::html($html);
    }

    private static function buildJsonLdSchemas(array $settings, array $faqs): string
    {
        $baseUrl = env('APP_URL', 'http://localhost:8000');
        
        // HomeAndConstructionBusiness Schema
        $businessSchema = [
            '@context' => 'https://schema.org',
            '@type'    => 'HomeAndConstructionBusiness',
            'name'     => $settings['company_pt_name'] ?? 'PT Rancang Bangun Sedaya',
            'alternateName' => $settings['brand_name'] ?? 'Rancang Bangun Kreasi (RBK)',
            'url'      => $baseUrl,
            'telephone'=> $settings['phone_number'] ?? '+62 812-3459-3742',
            'email'    => $settings['contact_email'] ?? 'rancangbangunkreasi.official@gmail.com',
            'address'  => [
                '@type' => 'PostalAddress',
                'streetAddress' => 'Pasirmulya',
                'addressLocality' => 'Kota Bogor',
                'postalCode' => '16118',
                'addressCountry' => 'ID'
            ],
            'openingHours' => 'Mo-Sa 08:00-17:00',
            'areaServed' => ['Jakarta', 'Bogor', 'Depok', 'Tangerang', 'Bekasi'],
            'priceRange' => '$$$'
        ];

        // Service Schemas (RBK Studio & RBK Konstruksi)
        $studioService = [
            '@context' => 'https://schema.org',
            '@type'    => 'Service',
            'name'     => 'RBK Studio — Jasa Arsitek & Perencanaan',
            'provider' => ['@type' => 'Organization', 'name' => 'RBK Studio'],
            'areaServed' => 'Jabodetabek',
            'offers'   => [
                '@type' => 'Offer',
                'price' => '60000',
                'priceCurrency' => 'IDR'
            ]
        ];

        $konstruksiService = [
            '@context' => 'https://schema.org',
            '@type'    => 'Service',
            'name'     => 'RBK Konstruksi — Jasa Bangun Rumah',
            'provider' => ['@type' => 'Organization', 'name' => 'RBK Konstruksi'],
            'areaServed' => 'Bogor',
            'offers'   => [
                '@type' => 'Offer',
                'price' => '4000000',
                'priceCurrency' => 'IDR'
            ]
        ];

        // FAQPage Schema
        $faqItems = [];
        foreach ($faqs as $f) {
            $faqItems[] = [
                '@type' => 'Question',
                'name'  => $f['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => strip_tags($f['answer'])
                ]
            ];
        }

        $faqSchema = [
            '@context' => 'https://schema.org',
            '@type'    => 'FAQPage',
            'mainEntity' => $faqItems
        ];

        return '<script type="application/ld+json">' . json_encode($businessSchema, JSON_UNESCAPED_UNICODE) . '</script>' . "\n" .
               '<script type="application/ld+json">' . json_encode($studioService, JSON_UNESCAPED_UNICODE) . '</script>' . "\n" .
               '<script type="application/ld+json">' . json_encode($konstruksiService, JSON_UNESCAPED_UNICODE) . '</script>' . "\n" .
               '<script type="application/ld+json">' . json_encode($faqSchema, JSON_UNESCAPED_UNICODE) . '</script>';
    }
}
