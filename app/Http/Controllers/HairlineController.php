<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\FrontData\AboutUsDataService;
use App\Services\FrontData\CatalogDataService;
use App\Services\FrontData\CatalogMenuDataService;
use App\Services\FrontData\SalonInfoDataService;
use App\Services\FrontData\SpecialistsDataService;
use Carbon\Carbon;
use Illuminate\View\View;

class HairlineController extends Controller
{
    public function __construct(
        private readonly SalonInfoDataService $salonInfo,
        private readonly CatalogDataService $catalog,
        private readonly SpecialistsDataService $specialists,
        private readonly CatalogMenuDataService $catalogMenu,
        private readonly AboutUsDataService $aboutUs
    ) {}

    public function show(string $initialView = 'home'): View
    {
        $identity = $this->salonInfo->getSalonIdentity();

        $initialBookings = Booking::with(['customer', 'items.service.category', 'items.specialist'])
            ->whereIn('status', ['confirmed', 'pending', 'in_progress', 'completed'])
            ->orderByDesc('id')
            ->limit(20)
            ->get()
            ->map(function (Booking $booking): array {
                $firstItem = $booking->items->first();
                $service = $firstItem?->service;
                $specialist = $firstItem?->specialist;

                $startTimeFormatted = '05:30 م';
                if ($booking->overall_start_time) {
                    $parsedTime = Carbon::parse($booking->overall_start_time);
                    $startTimeFormatted = $parsedTime->format('h:i').($parsedTime->format('A') === 'AM' ? ' ص' : ' م');
                }

                return [
                    'id' => $booking->booking_number,
                    'db_id' => $booking->id,
                    'customerName' => $booking->customer?->name ?? 'عميلة هيرلاين',
                    'customerPhone' => $booking->customer?->phone ?? '',
                    'customerEmail' => $booking->customer?->email,
                    'categoryTitle' => $service?->category?->getTranslation('name', 'ar') ?? 'المعالجات الحارة',
                    'serviceName' => $service?->getTranslation('name', 'ar') ?? 'البروتين بالتمليس الحراري',
                    'specialistName' => $specialist ? $specialist->getTranslation('name', 'ar') : 'الأخصائية نورة العتيبي',
                    'date' => $booking->booking_date ? $booking->booking_date->format('Y-m-d') : 'الأحد القادم',
                    'time' => $startTimeFormatted,
                    'price' => ((int) $booking->total_amount).' ر.س',
                ];
            })
            ->values()
            ->all();

        return view('hairline', [
            'initialView' => $initialView,
            'salonIdentity' => $identity,
            'heroSlides' => $identity['hero_slides'],
            'stats' => $identity['stats'],
            'aboutUs' => $this->aboutUs->getAboutUsContent(),
            'contactLinks' => $this->salonInfo->getContactLinks(),
            'workingHours' => $this->salonInfo->getWorkingHours(),
            'location' => $this->salonInfo->getLocation(),
            'walkInServices' => $this->catalog->getWalkInServicesGrouped(),
            'bookableCategories' => $this->catalog->getBookableCategoriesWithSpecialists(),
            'specialistsData' => $this->specialists->getAllSpecialists(),
            'menuPages' => $this->catalogMenu->getMenuPages(),
            'initialBookings' => $initialBookings,
        ]);
    }
}
