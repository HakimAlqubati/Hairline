<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Services\FrontData\CustomerAccountDataService;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Profile / My Bookings page.
 *
 * Allows login via customer code (e.g. HL-1001) or phone number.
 * After login, shows booking history and invoices from static data + session.
 */
#[Layout('layouts.app')]
#[Title('ملفي وحجوزاتي | HAIRLINE BEAUTY LOUNGE')]
class Profile extends Component
{
    public string $loginIdentifier = '';

    public bool $isLoggedIn = false;

    public string $loginError = '';

    /** @var array<string, mixed>|null */
    public ?array $customer = null;

    /** @var array<int, array<string, mixed>> */
    public array $bookingHistory = [];

    /** @var array<int, array<string, mixed>> */
    public array $invoices = [];

    public string $activeTab = 'bookings'; // 'bookings' | 'invoices'

    public function mount(CustomerAccountDataService $accountService): void
    {
        // Check for session-based bookings from the booking flow
        $sessionHistory = session('customer.booking_history', []);
        if (! empty($sessionHistory)) {
            // Auto-show session bookings without login
            $this->bookingHistory = array_reverse($sessionHistory);
        }
    }

    public function login(CustomerAccountDataService $accountService): void
    {
        $this->loginError = '';

        if (empty(trim($this->loginIdentifier))) {
            $this->loginError = 'يرجى إدخال كود العميلة أو رقم الجوال.';

            return;
        }

        $result = $accountService->login($this->loginIdentifier);

        if (! $result['success']) {
            $this->loginError = $result['message'];

            return;
        }

        $this->customer = $result['customer'];
        $this->isLoggedIn = true;

        $code = $this->customer['customer_code'];
        $staticHistory = $accountService->getBookingHistory($code);
        $sessionHistory = session('customer.booking_history', []);
        $this->bookingHistory = array_merge(array_reverse($sessionHistory), $staticHistory);
        $this->invoices = $accountService->getInvoices($code);
    }

    public function logout(): void
    {
        $this->isLoggedIn = false;
        $this->customer = null;
        $this->bookingHistory = session('customer.booking_history', []);
        $this->invoices = [];
        $this->loginIdentifier = '';
        $this->loginError = '';
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function render(): View
    {
        return view('livewire.pages.profile');
    }
}
