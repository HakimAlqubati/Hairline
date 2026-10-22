<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    protected static string $layout = 'filament-panels::components.layout.base';

    protected string $view = 'filament.pages.auth.login';

    public function getTitle(): string|Htmlable
    {
        return __('admin.auth.login_title');
    }

    public function getHeading(): string|Htmlable|null
    {
        return __('admin.auth.login_heading');
    }

    public function getSubheading(): string|Htmlable|null
    {
        return __('admin.auth.login_subheading');
    }
}
