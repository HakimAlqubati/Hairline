<?php

declare(strict_types=1);

use App\Filament\Resources\Settings\Pages\CreateSetting;
use App\Models\Setting;
use App\Models\User;
use App\Services\FrontData\AboutUsDataService;
use App\Services\FrontData\SalonInfoDataService;
use Livewire\Livewire;

test('Setting model can store, retrieve, and update key-value pairs', function () {
    Setting::setSetting('test_salon_name', 'صالون هيرلاين التجريبي');
    expect(Setting::getSetting('test_salon_name'))->toBe('صالون هيرلاين التجريبي');
    expect(Setting::get('test_salon_name'))->toBe('صالون هيرلاين التجريبي');

    // Update existing setting
    Setting::setSetting('test_salon_name', 'هيرلاين VIP');
    expect(Setting::getSetting('test_salon_name'))->toBe('هيرلاين VIP');

    // Default value fallback
    expect(Setting::getSetting('non_existing_key', 'الافتراضي'))->toBe('الافتراضي');
});

test('authenticated user can access settings page in filament admin', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/admin/settings');

    $response->assertSuccessful();
    $response->assertSee(__('admin.settings.label'));
    $response->assertSee(__('admin.settings.tabs.general'));
    $response->assertSee(__('admin.settings.tabs.contact'));
    $response->assertSee(__('admin.settings.tabs.social'));
    $response->assertSee(__('admin.settings.tabs.about'));
    $response->assertSee(__('admin.settings.tabs.seo'));
});

test('CreateSetting livewire page mounts with existing settings and saves updates', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(CreateSetting::class)
        ->assertFormSet([
            'site_name_ar' => 'هيرلاين',
            'contact_phone' => '0548857705',
            'founder_name' => 'سارة السيف',
        ])
        ->fillForm([
            'site_name_ar' => 'صالون هيرلاين الفاخر',
            'contact_phone' => '0541112233',
            'instagram_handle' => '@hairline.vip',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Setting::getSetting('site_name_ar'))->toBe('صالون هيرلاين الفاخر');
    expect(Setting::getSetting('contact_phone'))->toBe('0541112233');
    expect(Setting::getSetting('instagram_handle'))->toBe('@hairline.vip');
});

test('about page data services reflect content from settings', function () {
    Setting::setSetting('about_title', 'قصتنا وهويتنا');
    Setting::setSetting('about_founding_year', 2019);
    Setting::setSetting('about_intro', 'مقدمة تجريبية خاصة بهيرلاين من الإعدادات');
    Setting::setSetting('about_description', 'وصف تجريبي خاص ومطور');
    Setting::setSetting('about_belief', 'إيماننا بالجمال الحقيقي');
    Setting::setSetting('founder_name', 'نورة العبدالله');
    Setting::setSetting('founder_title', 'المديرة التنفيذية والمؤسسة');
    Setting::setSetting('default_working_hours', 'السبت إلى الخميس: 10 ص حتى 10 م');
    Setting::setSetting('contact_address', 'الرياض - حي النرجس، شارع أنس بن مالك');
    Setting::setSetting('contact_phone', '0549998877');
    Setting::setSetting('instagram_handle', '@hairline_new');

    $aboutService = app(AboutUsDataService::class);
    $aboutContent = $aboutService->getAboutUsContent();

    expect($aboutContent['title'])->toBe('قصتنا وهويتنا')
        ->and($aboutContent['founding_year'])->toBe(2019)
        ->and($aboutContent['intro_paragraph'])->toBe('مقدمة تجريبية خاصة بهيرلاين من الإعدادات')
        ->and($aboutContent['description_paragraph'])->toBe('وصف تجريبي خاص ومطور')
        ->and($aboutContent['belief_paragraph'])->toBe('إيماننا بالجمال الحقيقي')
        ->and($aboutContent['founder_signature']['name_ar'])->toBe('نورة العبدالله')
        ->and($aboutContent['founder_signature']['title_ar'])->toBe('المديرة التنفيذية والمؤسسة');

    $salonInfoService = app(SalonInfoDataService::class);
    $workingHours = $salonInfoService->getWorkingHours();
    $location = $salonInfoService->getLocation();
    $contactLinks = $salonInfoService->getContactLinks();

    expect($workingHours['hours_ar'])->toBe('السبت إلى الخميس: 10 ص حتى 10 م')
        ->and($location['full_address_ar'])->toBe('الرياض - حي النرجس، شارع أنس بن مالك')
        ->and($contactLinks['instagram_handle'])->toBe('@hairline_new')
        ->and($contactLinks['phone'])->toBe('0549998877');

    $response = $this->get('/?tab=about');
    $response->assertSuccessful();
    $response->assertSee('قصتنا وهويتنا');
    $response->assertSee('مقدمة تجريبية خاصة بهيرلاين من الإعدادات');
    $response->assertSee('نورة العبدالله');
    $response->assertSee('السبت إلى الخميس: 10 ص حتى 10 م');
    $response->assertSee('الرياض - حي النرجس، شارع أنس بن مالك');
});
