<?php

declare(strict_types=1);

namespace App\Filament\Resources\SiteVisits\Tables;

use App\Models\SiteVisit;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SiteVisitTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with('customer:id,name'))
            ->columns([
                TextColumn::make('visited_at')
                    ->label(__('admin.site_visits.columns.visited_at'))
                    ->dateTime('Y-m-d H:i', 'Asia/Riyadh')
                    ->description(fn (SiteVisit $record): string => $record->visited_at
                        ? $record->visited_at->timezone('Asia/Riyadh')->locale(app()->getLocale())->diffForHumans()
                        : '')
                    ->tooltip(fn (SiteVisit $record): ?string => $record->visited_at?->timezone('Asia/Riyadh')->format('Y-m-d H:i:s'))
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('device_type')
                    ->label(__('admin.site_visits.columns.device_type'))
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => SiteVisit::deviceTypeOptions()[$state ?? SiteVisit::DEVICE_UNKNOWN]
                        ?? __('admin.site_visits.device_types.unknown'))
                    ->icon(fn (?string $state): string => match ($state) {
                        SiteVisit::DEVICE_MOBILE => 'heroicon-o-device-phone-mobile',
                        SiteVisit::DEVICE_TABLET => 'heroicon-o-device-tablet',
                        SiteVisit::DEVICE_DESKTOP => 'heroicon-o-computer-desktop',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        SiteVisit::DEVICE_MOBILE => 'success',
                        SiteVisit::DEVICE_TABLET => 'warning',
                        SiteVisit::DEVICE_DESKTOP => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('device_name')
                    ->label(__('admin.site_visits.columns.device_and_os'))
                    ->state(function (SiteVisit $record): string {
                        if (filled($record->device_name)) {
                            return $record->device_name;
                        }

                        if (filled($record->os) && $record->os !== 'Unknown') {
                            return $record->os;
                        }

                        return __('admin.site_visits.unknown');
                    })
                    ->description(fn (SiteVisit $record): ?string => filled($record->os) && $record->os !== 'Unknown'
                        ? $record->os
                        : null)
                    ->searchable(['device_name', 'os']),

                TextColumn::make('browser')
                    ->label(__('admin.site_visits.columns.browser'))
                    ->badge()
                    ->default(fn (): string => __('admin.site_visits.other_browser'))
                    ->color(fn (?string $state): string => match ($state) {
                        'Safari', 'Chrome' => 'info',
                        'Instagram', 'TikTok', 'Snapchat' => 'warning',
                        'WhatsApp' => 'success',
                        default => 'gray',
                    })
                    ->searchable(),

                TextColumn::make('page_name')
                    ->label(__('admin.site_visits.columns.page'))
                    ->state(fn (SiteVisit $record): string => SiteVisit::pageNameOptions()[$record->page_name ?? '']
                        ?? $record->path)
                    ->description(fn (SiteVisit $record): string => $record->path)
                    ->weight('bold')
                    ->searchable(['page_name', 'path']),

                TextColumn::make('customer.name')
                    ->label(__('admin.site_visits.columns.visitor'))
                    ->badge()
                    ->default(fn (): string => __('admin.site_visits.guest_visitor'))
                    ->color(fn (SiteVisit $record): string => $record->customer_id ? 'success' : 'gray')
                    ->description(fn (SiteVisit $record): ?string => $record->customer_id
                        ? __('admin.site_visits.registered_customer')
                        : null)
                    ->searchable(),

                TextColumn::make('referer_host')
                    ->label(__('admin.site_visits.columns.source'))
                    ->default(fn (): string => __('admin.site_visits.direct_visit'))
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('ip_address')
                    ->label(__('admin.site_visits.columns.ip_address'))
                    ->copyable()
                    ->placeholder('-')
                    ->searchable()
                    ->toggleable(),
            ])
            ->defaultSort('visited_at', 'desc')
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100])
            ->searchPlaceholder(__('admin.site_visits.search_placeholder'))
            ->emptyStateHeading(__('admin.site_visits.empty_heading'))
            ->emptyStateDescription(__('admin.site_visits.empty_description'))
            ->emptyStateIcon('heroicon-o-eye')
            ->filters([
                Filter::make('today')
                    ->label(__('admin.site_visits.filters.today_only'))
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->where(
                        'visited_at',
                        '>=',
                        Carbon::now('Asia/Riyadh')->startOfDay()->utc()->toDateTimeString()
                    )),

                SelectFilter::make('device_type')
                    ->label(__('admin.site_visits.filters.device_type'))
                    ->options(SiteVisit::deviceTypeOptions()),

                SelectFilter::make('os')
                    ->label(__('admin.site_visits.filters.os'))
                    ->options(SiteVisit::osOptions()),

                SelectFilter::make('page_name')
                    ->label(__('admin.site_visits.filters.page'))
                    ->options(SiteVisit::pageNameOptions()),

                Filter::make('visited_at')
                    ->label(__('admin.site_visits.filters.date_range'))
                    ->schema([
                        DatePicker::make('from')
                            ->label(__('admin.site_visits.filters.from_date')),
                        DatePicker::make('until')
                            ->label(__('admin.site_visits.filters.until_date')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $q, string $date): Builder => $q->where(
                                    'visited_at',
                                    '>=',
                                    Carbon::parse($date, 'Asia/Riyadh')->startOfDay()->utc()->toDateTimeString()
                                )
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $q, string $date): Builder => $q->where(
                                    'visited_at',
                                    '<=',
                                    Carbon::parse($date, 'Asia/Riyadh')->endOfDay()->utc()->toDateTimeString()
                                )
                            );
                    }),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
