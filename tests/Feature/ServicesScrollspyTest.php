<?php

declare(strict_types=1);

use App\Enums\BrandColor;
use App\Models\CatalogPage;
use App\Services\FrontData\CatalogMenuDataService;

describe('Services Sticky Scrollspy Backend Integration', function () {
    test('scrollspy sections are dynamically built from active catalog pages in database order', function () {
        $sections = app(CatalogMenuDataService::class)->getScrollspySections();

        expect($sections)->toHaveCount(10);
        expect($sections[0]['id'])->toBe('service-section-1');
        expect($sections[0]['title_ar'])->toBe('خدمات الشعر');
        expect($sections[9]['id'])->toBe('service-section-8');
        expect($sections[9]['title_ar'])->toBe('أطوال الشعر');
    });

    test('updating or deactivating a catalog page dynamically updates scrollspy navigation and rendered services page', function () {
        $pageFour = CatalogPage::where('page_number', 4)->firstOrFail();
        $pageFive = CatalogPage::where('page_number', 5)->firstOrFail();

        $pageFour->setTranslation('title', 'ar', 'تسريحات الاستشوار الملكية');
        $pageFour->save();

        $pageFive->update(['is_active' => false]);

        $sections = app(CatalogMenuDataService::class)->getScrollspySections();
        $sectionIds = array_column($sections, 'id');

        expect($sections)->toHaveCount(9);
        expect($sectionIds)->not->toContain('service-section-5');
        expect(collect($sections)->firstWhere('id', 'service-section-4')['title_ar'])->toBe('تسريحات الاستشوار الملكية');

        $response = $this->get('/services');
        $response->assertSuccessful();
        $response->assertSee('data-scrollspy-pill="service-section-4"', false);
        $response->assertSee('تسريحات الاستشوار الملكية');
        $response->assertDontSee('data-scrollspy-pill="service-section-5"', false);
        $response->assertDontSee('data-scrollspy-section="service-section-5"', false);
    });

    test('services page renders scrollspy active state using salon brand primary gold color', function () {
        $response = $this->get('/services');

        $response->assertSuccessful();
        $response->assertSee('--color-primary: '.BrandColor::Primary->value, false);
        $response->assertSee('id="servicesStickyBar"', false);
        $response->assertSee('id="servicesScrollspyPills"', false);
        $response->assertSee('id="servicesScrollspyMenuBtn"', false);
        $response->assertSee('bg-primary text-white border border-primary', false);
        $response->assertSee('data-scrollspy-section="service-section-6-0"', false);
        $response->assertSee('data-scrollspy-section="service-section-7-1"', false);
    });
});
