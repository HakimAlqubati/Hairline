<?php

declare(strict_types=1);

use App\Filament\Resources\Settings\SettingResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;

test('users and settings resources have no navigation group', function () {
    expect(UserResource::getNavigationGroup())->toBeNull();
    expect(SettingResource::getNavigationGroup())->toBeNull();
});

test('user resource labels are set without supervisors', function () {
    app()->setLocale('ar');
    expect(UserResource::getPluralModelLabel())->toBe('المستخدمين');
    expect(UserResource::getModelLabel())->toBe('مستخدم');

    app()->setLocale('en');
    expect(UserResource::getPluralModelLabel())->toBe('Users');
    expect(UserResource::getModelLabel())->toBe('User');
});

test('authenticated user sees users and settings in navigation without group', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/admin');

    $response->assertSuccessful();
    $response->assertSee('المستخدمين');
    $response->assertSee('إعدادات النظام');
    $response->assertDontSee('إعدادات النظام والمشرفين');
});
