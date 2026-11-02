<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\Settings\SettingResource;
use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateSetting extends CreateRecord
{
    protected static string $resource = SettingResource::class;

    protected static bool $canCreateAnother = false;

    public function mount(): void
    {
        parent::mount();

        // Pre-fill the form with existing settings
        $settings = Setting::pluck('value', 'key')->toArray();

        foreach ($settings as $key => $value) {
            if (is_string($value) && (str_starts_with($value, '{') || str_starts_with($value, '['))) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $settings[$key] = $decoded;
                }
            }
        }

        $this->form->fill($settings);
    }

    public function create(bool $another = false): void
    {
        $data = $this->form->getState();

        $excludedKeys = ['site_logo', 'site_favicon', 'og_image'];
        $finalSettings = [];
        $now = now();

        foreach ($data as $key => $value) {
            if (! in_array($key, $excludedKeys, true) && is_array($value)) {
                $finalSettings[] = [
                    'key' => $key,
                    'value' => json_encode($value, JSON_UNESCAPED_UNICODE),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            } else {
                $finalSettings[] = [
                    'key' => $key,
                    'value' => is_bool($value) ? ($value ? '1' : '0') : (string) ($value ?? ''),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (! empty($finalSettings)) {
            Setting::upsert($finalSettings, ['key'], ['value', 'updated_at']);
        }

        $this->sendSuccessNotification(
            __('admin.settings.saved_title') ?? 'تم الحفظ',
            __('admin.settings.saved_body') ?? 'تم حفظ الإعدادات بنجاح'
        );
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('admin.actions.save') ?? 'حفظ الإعدادات')
                ->submit('create')
                ->action('create')
                ->color('primary')
                ->keyBindings(['mod+s']),
            Action::make('cancel')
                ->url(static::getResource()::getUrl())
                ->label(__('admin.actions.cancel') ?? 'إلغاء')
                ->color('gray'),
        ];
    }

    private function sendSuccessNotification(string $title, string $message): Notification
    {
        return Notification::make()
            ->title($title)
            ->body($message)
            ->icon('heroicon-o-check-circle')
            ->duration(3000)
            ->iconColor('success')
            ->success()
            ->send();
    }

    public function getTitle(): string|Htmlable
    {
        return __('admin.settings.label') ?? 'إعدادات النظام';
    }
}
