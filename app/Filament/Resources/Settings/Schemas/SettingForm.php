<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make(__('admin.settings.label'))
                    ->columnSpanFull()
                    ->schema([
                        // Tab 1: General Settings
                        Tab::make(__('admin.settings.tabs.general'))
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('site_name_ar')
                                        ->label(__('admin.settings.fields.site_name_ar'))
                                        ->required(),

                                    TextInput::make('site_name_en')
                                        ->label(__('admin.settings.fields.site_name_en')),

                                    TextInput::make('site_tagline_ar')
                                        ->label(__('admin.settings.fields.site_tagline_ar')),

                                    TextInput::make('site_tagline_en')
                                        ->label(__('admin.settings.fields.site_tagline_en')),
                                ]),

                                Grid::make(2)->schema([
                                    Textarea::make('description_line1')
                                        ->label(__('admin.settings.fields.description_line1'))
                                        ->rows(2),

                                    Textarea::make('description_line2')
                                        ->label(__('admin.settings.fields.description_line2'))
                                        ->rows(2),
                                ]),

                                Grid::make(3)->schema([
                                    TextInput::make('tax_number')
                                        ->label(__('admin.settings.fields.tax_number')),

                                    TextInput::make('vat_percentage')
                                        ->label(__('admin.settings.fields.vat_percentage'))
                                        ->numeric()
                                        ->suffix('%'),

                                    TextInput::make('deposit_percentage')
                                        ->label(__('admin.settings.fields.deposit_percentage'))
                                        ->numeric()
                                        ->suffix('%'),
                                ]),

                                Grid::make(2)->schema([
                                    TextInput::make('currency')
                                        ->label(__('admin.settings.fields.currency'))
                                        ->default('SAR'),

                                    TextInput::make('default_working_hours')
                                        ->label(__('admin.settings.fields.default_working_hours')),
                                ]),

                                Grid::make(2)->schema([
                                    FileUpload::make('site_logo')
                                        ->label(__('admin.settings.fields.site_logo'))
                                        ->image()
                                        ->directory('settings')
                                        ->disk('public'),

                                    FileUpload::make('site_favicon')
                                        ->label(__('admin.settings.fields.site_favicon'))
                                        ->image()
                                        ->directory('settings')
                                        ->disk('public'),
                                ]),
                            ]),

                        // Tab 2: Contact & Bookings
                        Tab::make(__('admin.settings.tabs.contact'))
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('contact_phone')
                                        ->label(__('admin.settings.fields.contact_phone'))
                                        ->tel(),

                                    TextInput::make('whatsapp_number')
                                        ->label(__('admin.settings.fields.whatsapp_number'))
                                        ->tel(),

                                    TextInput::make('contact_email')
                                        ->label(__('admin.settings.fields.contact_email'))
                                        ->email(),

                                    TextInput::make('google_maps_url')
                                        ->label(__('admin.settings.fields.google_maps_url'))
                                        ->url(),
                                ]),

                                Textarea::make('contact_address')
                                    ->label(__('admin.settings.fields.contact_address'))
                                    ->rows(2)
                                    ->columnSpanFull(),

                                Textarea::make('google_maps_iframe')
                                    ->label(__('admin.settings.fields.google_maps_iframe'))
                                    ->rows(3)
                                    ->columnSpanFull(),

                                Grid::make(2)->schema([
                                    TextInput::make('latitude')
                                        ->label(__('admin.settings.fields.latitude'))
                                        ->numeric(),

                                    TextInput::make('longitude')
                                        ->label(__('admin.settings.fields.longitude'))
                                        ->numeric(),
                                ]),
                            ]),

                        // Tab 3: Social Media Accounts
                        Tab::make(__('admin.settings.tabs.social'))
                            ->icon('heroicon-o-share')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('instagram_url')
                                        ->label(__('admin.settings.fields.instagram_url'))
                                        ->url(),

                                    TextInput::make('instagram_handle')
                                        ->label(__('admin.settings.fields.instagram_handle'))
                                        ->placeholder('@hairline.sa'),

                                    TextInput::make('snapchat_url')
                                        ->label(__('admin.settings.fields.snapchat_url'))
                                        ->url(),

                                    TextInput::make('snapchat_handle')
                                        ->label(__('admin.settings.fields.snapchat_handle'))
                                        ->placeholder('@hairline.sa'),

                                    TextInput::make('tiktok_url')
                                        ->label(__('admin.settings.fields.tiktok_url'))
                                        ->url(),

                                    TextInput::make('tiktok_handle')
                                        ->label(__('admin.settings.fields.tiktok_handle'))
                                        ->placeholder('@hairline.sa'),

                                    TextInput::make('facebook_url')
                                        ->label(__('admin.settings.fields.facebook_url'))
                                        ->url(),

                                    TextInput::make('twitter_url')
                                        ->label(__('admin.settings.fields.twitter_url'))
                                        ->url(),
                                ]),
                            ]),

                        // Tab 4: About Us & Founder Story
                        Tab::make(__('admin.settings.tabs.about'))
                            ->icon('heroicon-o-sparkles')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('about_title')
                                        ->label(__('admin.settings.fields.about_title'))
                                        ->default('من نحن'),

                                    TextInput::make('about_founding_year')
                                        ->label(__('admin.settings.fields.about_founding_year'))
                                        ->numeric()
                                        ->default(2020),
                                ]),

                                Textarea::make('about_intro')
                                    ->label(__('admin.settings.fields.about_intro'))
                                    ->rows(2)
                                    ->columnSpanFull(),

                                Textarea::make('about_description')
                                    ->label(__('admin.settings.fields.about_description'))
                                    ->rows(3)
                                    ->columnSpanFull(),

                                Textarea::make('about_belief')
                                    ->label(__('admin.settings.fields.about_belief'))
                                    ->rows(3)
                                    ->columnSpanFull(),

                                TextInput::make('founder_message_title')
                                    ->label(__('admin.settings.fields.founder_message_title'))
                                    ->default('كلمة المؤسسة')
                                    ->columnSpanFull(),

                                Textarea::make('founder_message_p1')
                                    ->label(__('admin.settings.fields.founder_message_p1'))
                                    ->rows(3)
                                    ->columnSpanFull(),

                                Textarea::make('founder_message_p2')
                                    ->label(__('admin.settings.fields.founder_message_p2'))
                                    ->rows(3)
                                    ->columnSpanFull(),

                                Textarea::make('founder_message_p3')
                                    ->label(__('admin.settings.fields.founder_message_p3'))
                                    ->rows(3)
                                    ->columnSpanFull(),

                                Grid::make(2)->schema([
                                    TextInput::make('founder_name')
                                        ->label(__('admin.settings.fields.founder_name'))
                                        ->default('سارة السيف'),

                                    TextInput::make('founder_title')
                                        ->label(__('admin.settings.fields.founder_title'))
                                        ->default('Founder | مؤسسة هيرلاين'),
                                ]),
                            ]),

                        // Tab 5: SEO & Web Meta
                        Tab::make(__('admin.settings.tabs.seo'))
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('meta_title')
                                        ->label(__('admin.settings.fields.meta_title'))
                                        ->placeholder('هيرلاين | HAIRLINE BEAUTY SALON')
                                        ->columnSpanFull(),

                                    Textarea::make('meta_description')
                                        ->label(__('admin.settings.fields.meta_description'))
                                        ->rows(3)
                                        ->columnSpanFull(),

                                    TextInput::make('meta_keywords')
                                        ->label(__('admin.settings.fields.meta_keywords'))
                                        ->columnSpanFull(),

                                    TextInput::make('google_site_verification')
                                        ->label(__('admin.settings.fields.google_site_verification')),

                                    TextInput::make('bing_site_verification')
                                        ->label(__('admin.settings.fields.bing_site_verification')),

                                    FileUpload::make('og_image')
                                        ->label(__('admin.settings.fields.og_image'))
                                        ->image()
                                        ->directory('settings')
                                        ->disk('public')
                                        ->columnSpanFull(),
                                ]),
                            ]),
                    ]),
            ]);
    }
}
