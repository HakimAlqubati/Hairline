<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\UpdateCustomerProfileRequest;
use App\Models\Customer;
use App\Services\Booking\BookingPayloadFormatter;
use App\Services\Customer\CustomerProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerProfileController extends Controller
{
    public function __construct(
        private readonly CustomerProfileService $profileService,
        private readonly BookingPayloadFormatter $payloadFormatter
    ) {}

    /**
     * Update the logged-in customer's profile (name, Saudi phone number, and optional avatar image).
     * Protected strictly by active session to prevent modifying other customers' records.
     */
    public function updateProfile(UpdateCustomerProfileRequest $request): JsonResponse
    {
        $customerId = session('logged_in_customer_id');
        $customer = $customerId ? Customer::find($customerId) : null;

        if (! $customer) {
            return response()->json([
                'success' => false,
                'status' => 'unauthenticated',
                'message' => 'يرجى تسجيل الدخول أو إتمام حجز أولاً لتعديل الملف الشخصي.',
            ], 401);
        }

        $updatedCustomer = $this->profileService->updateCustomer(
            customer: $customer,
            name: (string) $request->input('name'),
            phone: $request->input('phone'),
            avatarFile: $request->file('avatar')
        );

        return response()->json([
            'success' => true,
            'status' => 'updated',
            'message' => 'تم تحديث بيانات ملفكِ الشخصي بنجاح.',
            'customer' => $this->payloadFormatter->formatCustomerPayload($updatedCustomer),
            'customer_bookings' => $this->payloadFormatter->getCustomerBookingsPayload($updatedCustomer),
        ]);
    }

    /**
     * Log out the currently authenticated customer from the session.
     */
    public function logout(Request $request): JsonResponse
    {
        $this->profileService->logout();

        return response()->json([
            'success' => true,
            'status' => 'logged_out',
            'message' => 'تم تسجيل الخروج بنجاح.',
        ]);
    }
}
