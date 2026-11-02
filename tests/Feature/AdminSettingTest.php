<?php

declare(strict_types=1);

use App\Filament\Resources\Settings\Pages\CreateSetting;
use App\Models\Setting;
use App\Models\User;
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
