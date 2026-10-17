<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Services\FrontData\BookingCalendarDataService;
use App\Services\FrontData\CartAndCheckoutDataService;
use App\Services\FrontData\CatalogDataService;
use App\Services\FrontData\HomeServiceDataService;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Booking flow component — multi-step:
 *  1. Service selection (with category tabs)
 *  2. Location type (salon / home)
 *  3. Date + specialist selection
 *  4. Time slot selection (conflict-aware)
 *  5. Client info form
 *  6. Confirmation ticket
 */
#[Layout('layouts.app')]
#[Title('الحجز | HAIRLINE BEAUTY LOUNGE')]
class Booking extends Component
{
    // ── Form fields ──────────────────────────────────────────
    public string $activeCategoryId = 'hair';

    /** @var string[] */
    public array $selectedServiceIds = [];

    public string $bookingType = 'salon'; // 'salon' | 'home'

    public string $selectedDistrictId = '';

    public string $selectedSpecialistId = 'sp1';

    public string $selectedDateId = 'd1';

    public string $selectedTime = '';

    public string $clientName = '';

    public string $clientPhone = '';

    public string $selectedPaymentId = 'pay_at_salon';

    // ── UI State ──────────────────────────────────────────────
    public bool $isConfirmed = false;

    public bool $showCancellationSheet = false;

    public bool $showDistrictSheet = false;

    public bool $showPaymentSheet = false;

    /** @var array<string, string> */
    public array $validationErrors = [];

    /** @var array<string, mixed> */
    public array $confirmedBooking = [];

    // ── Loaded Data ───────────────────────────────────────────
    /** @var array<int, array<string, mixed>> */
    public array $categories = [];

    /** @var array<int, array<string, mixed>> */
    public array $specialists = [];

    /** @var array<int, array<string, mixed>> */
    public array $availableDates = [];

    /** @var array<int, array<string, mixed>> */
    public array $timeSlots = [];

    /** @var array<int, array<string, mixed>> */
    public array $paymentOptions = [];

    /** @var array<string, mixed> */
    public array $cancellationPolicy = [];

    /** @var array<string, mixed> */
    public array $homeServiceDetails = [];

    /** @var array<string, array<int, array<string, mixed>>> */
    public array $districtGroups = [];

    public function mount(
        CatalogDataService $catalog,
        BookingCalendarDataService $calendar,
        CartAndCheckoutDataService $cart,
        HomeServiceDataService $homeService
    ): void {
        $this->categories = $catalog->getAllCategories();
        $this->specialists = $calendar->getSpecialists();
        $this->availableDates = $calendar->getAvailableDates();
        $this->paymentOptions = $cart->getPaymentOptions();
        $this->cancellationPolicy = $cart->getCancellationPolicy();
        $this->homeServiceDetails = $homeService->getHomeServiceDetails();
        $this->districtGroups = $homeService->getCoveredDistrictsGrouped();

        // Pre-select first specialist and set default time
        $this->selectedSpecialistId = $this->specialists[0]['id'] ?? 'sp1';
        $this->selectedDateId = $this->availableDates[0]['id'] ?? 'd1';

        // Load from session if user came from Services page
        $sessionServices = session('booking.selected_services', []);
        $sessionCategory = session('booking.active_category', 'hair');

        if (! empty($sessionServices)) {
            $this->selectedServiceIds = $sessionServices;
            $this->activeCategoryId = $sessionCategory;
            session()->forget(['booking.selected_services', 'booking.active_category']);
        }

        $this->refreshTimeSlots($calendar);
    }

    public function updatedSelectedSpecialistId(string $value): void
    {
        $this->refreshTimeSlots(app(BookingCalendarDataService::class));
    }

    public function updatedSelectedDateId(string $value): void
    {
        $this->refreshTimeSlots(app(BookingCalendarDataService::class));
        $this->selectedTime = '';
    }

    public function updatedBookingType(): void
    {
        $this->refreshTimeSlots(app(BookingCalendarDataService::class));
        $this->selectedTime = '';
        $this->selectedSpecialistId = $this->bookingType === 'home'
            ? ($this->getHomeSpecialist()['id'] ?? 'sp3')
            : ($this->specialists[0]['id'] ?? 'sp1');
    }

    private function refreshTimeSlots(BookingCalendarDataService $calendar): void
    {
        $this->timeSlots = $calendar->getAvailableSlots(
            $this->selectedSpecialistId,
            $this->selectedDateId,
            $this->bookingType
        );

        // Auto-select first available time if none selected
        if (empty($this->selectedTime)) {
            foreach ($this->timeSlots as $slot) {
                if ($slot['is_available']) {
                    $this->selectedTime = $slot['time'];
                    break;
                }
            }
        }
    }

    public function selectCategory(string $categoryId): void
    {
        $this->activeCategoryId = $categoryId;
    }

    public function toggleService(string $serviceId): void
    {
        if (in_array($serviceId, $this->selectedServiceIds, true)) {
            $this->selectedServiceIds = array_values(
                array_filter($this->selectedServiceIds, fn (string $id) => $id !== $serviceId)
            );
        } else {
            $this->selectedServiceIds[] = $serviceId;
        }
    }

    public function selectSpecialist(string $specialistId): void
    {
        $this->selectedSpecialistId = $specialistId;
    }

    public function selectDate(string $dateId): void
    {
        $this->selectedDateId = $dateId;
    }

    public function selectTime(string $time): void
    {
        $this->selectedTime = $time;
    }

    public function selectDistrict(string $districtId): void
    {
        $this->selectedDistrictId = $districtId;
        $this->showDistrictSheet = false;
    }

    public function submitBooking(
        CatalogDataService $catalog,
        CartAndCheckoutDataService $cart,
        BookingCalendarDataService $calendar
    ): void {
        $this->validationErrors = [];

        $validation = $cart->validateBookingForm([
            'service_ids' => $this->selectedServiceIds,
            'client_name' => $this->clientName,
            'client_phone' => $this->clientPhone,
        ]);

        if (! $validation['is_valid']) {
            $this->validationErrors = $validation['errors'];

            return;
        }

        // Conflict check on time slot
        if (! $calendar->isSlotAvailable($this->selectedSpecialistId, $this->selectedDateId, $this->selectedTime, $this->bookingType)) {
            $this->validationErrors['time'] = 'هذا الوقت محجوز بالفعل. يرجى اختيار وقت آخر.';

            return;
        }

        $selectedDate = collect($this->availableDates)->firstWhere('id', $this->selectedDateId);
        $dateLabel = ($selectedDate['day_name'] ?? '').' ('.($selectedDate['date_label'] ?? '').')';

        $districtName = null;
        if ($this->bookingType === 'home' && $this->selectedDistrictId) {
            foreach (app(HomeServiceDataService::class)->getAllDistricts() as $d) {
                if ($d['id'] === $this->selectedDistrictId) {
                    $districtName = $d['name'];
                    break;
                }
            }
        }

        $this->confirmedBooking = $cart->buildBookingSummary(
            $this->selectedServiceIds,
            [
                'client_name' => $this->clientName,
                'client_phone' => $this->clientPhone,
                'date_id' => $this->selectedDateId,
                'date_label' => $dateLabel,
                'time' => $this->selectedTime,
                'booking_type' => $this->bookingType,
                'district_id' => $this->selectedDistrictId,
                'district_name' => $districtName,
            ],
            $catalog
        );

        // Save to session for profile page
        $history = session('customer.booking_history', []);
        $history[] = $this->confirmedBooking;
        session(['customer.booking_history' => $history]);

        $this->isConfirmed = true;
        $this->selectedServiceIds = [];
    }

    public function resetBooking(): void
    {
        $this->isConfirmed = false;
        $this->confirmedBooking = [];
        $this->clientName = '';
        $this->clientPhone = '';
        $this->selectedTime = '';
        $this->selectedServiceIds = [];
    }

    public function copyRefCode(): void
    {
        // Dispatches JS to copy via clipboard API
        $this->dispatch('copy-to-clipboard', text: $this->confirmedBooking['ref_code'] ?? '');
    }

    /** @return array<string, mixed> */
    public function getActiveCategoryServicesProperty(CatalogDataService $catalog): array
    {
        foreach ($catalog->getAllCategories() as $cat) {
            if ($cat['id'] === $this->activeCategoryId) {
                return $cat['services'];
            }
        }

        return [];
    }

    private function getHomeSpecialist(): ?array
    {
        foreach ($this->specialists as $sp) {
            if ($sp['is_home_specialist']) {
                return $sp;
            }
        }

        return null;
    }

    public function render(CatalogDataService $catalog): View
    {
        return view('livewire.pages.booking', [
            'activeServices' => $this->getActiveCategoryServicesProperty($catalog),
            'totals' => $catalog->calculateTotals($this->selectedServiceIds),
            'deposit' => app(CartAndCheckoutDataService::class)->calculateDeposit(
                $catalog->calculateTotals($this->selectedServiceIds)['total_price']
            ),
        ]);
    }
}
