<?php

declare(strict_types=1);

namespace App\Services\FrontData;

use App\Models\CancellationPolicy;
use App\Models\PaymentMethod;

/**
 * Manages the in-session booking cart: service selection, totals,
 * deposit calculation, cancellation policy, and payment options backed by DB.
 */
class CartAndCheckoutDataService
{
    /** Deposit percentage of the total */
    public const DEPOSIT_PERCENTAGE = 30;

    /** Minimum deposit amount in SAR */
    public const DEPOSIT_MINIMUM_SAR = 50;

    /**
     * @return array{
     *     policy_ar: string,
     *     free_cancellation_hours: int,
     *     partial_refund_hours: int,
     *     partial_refund_percentage: int,
     *     no_show_fee_percentage: int
     * }
     */
    public function getCancellationPolicy(): array
    {
        $policy = CancellationPolicy::where('is_active', true)->first();

        return [
            'policy_ar' => $policy?->getTranslation('policy_text', 'ar') ?? 'يمكن إلغاء الحجز مجاناً قبل 12 ساعة من الموعد.',
            'free_cancellation_hours' => $policy?->free_cancellation_hours ?? 12,
            'partial_refund_hours' => $policy?->partial_refund_hours ?? 6,
            'partial_refund_percentage' => (int) ($policy?->partial_refund_percentage ?? 50),
            'no_show_fee_percentage' => (int) ($policy?->no_show_penalty_percentage ?? 100),
        ];
    }

    /**
     * @return array<int, array{id: string, name_ar: string, name_en: string, icon: string, is_available: bool, fee: int, description_ar: string}>
     */
    public function getPaymentOptions(): array
    {
        $methods = PaymentMethod::where('is_available', true)->orderBy('sort_order')->get();

        return $methods->map(fn (PaymentMethod $m) => [
            'id' => $m->code,
            'name_ar' => $m->getTranslation('name', 'ar'),
            'name_en' => $m->getTranslation('name', 'en'),
            'icon' => $m->icon,
            'is_available' => (bool) $m->is_available,
            'fee' => (int) $m->fee,
            'description_ar' => $m->getTranslation('description', 'ar'),
        ])->all();
    }

    /**
     * Calculate the booking deposit (عربون) amount.
     *
     * @return array{
     *     deposit_amount: int,
     *     deposit_percentage: int,
     *     remaining_amount: int,
     *     total_amount: int,
     *     deposit_label_ar: string
     * }
     */
    public function calculateDeposit(int $totalAmount): array
    {
        $depositAmount = (int) ceil($totalAmount * self::DEPOSIT_PERCENTAGE / 100);
        $depositAmount = max($depositAmount, self::DEPOSIT_MINIMUM_SAR);
        $depositAmount = min($depositAmount, $totalAmount);

        return [
            'deposit_amount' => $depositAmount,
            'deposit_percentage' => self::DEPOSIT_PERCENTAGE,
            'remaining_amount' => $totalAmount - $depositAmount,
            'total_amount' => $totalAmount,
            'deposit_label_ar' => 'عربون الحجز ('.self::DEPOSIT_PERCENTAGE.'٪)',
        ];
    }

    /**
     * Build the final booking summary object from form data and service details.
     *
     * @param  string[]  $serviceIds
     * @param  array{
     *     client_name: string,
     *     client_phone: string,
     *     date_label: string,
     *     time: string,
     *     specialist_id?: string,
     *     booking_type: string,
     *     district_id?: string,
     *     district_name?: string
     * }  $formData
     * @return array{
     *     ref_code: string,
     *     client_name: string,
     *     client_phone: string,
     *     date_label: string,
     *     time: string,
     *     booking_type: string,
     *     services: array<int, array<string, mixed>>,
     *     totals: array<string, mixed>,
     *     deposit: array<string, mixed>,
     *     district_name: string|null,
     *     status: string,
     *     created_at_label: string
     * }
     */
    public function buildBookingSummary(array $serviceIds, array $formData, CatalogDataService $catalog): array
    {
        $totals = $catalog->calculateTotals($serviceIds);
        $deposit = $this->calculateDeposit($totals['total_price']);
        $services = array_filter(
            array_map(fn (string $id) => $catalog->findServiceById($id), $serviceIds)
        );

        return [
            'ref_code' => $this->generateRefCode(),
            'client_name' => $formData['client_name'],
            'client_phone' => $formData['client_phone'],
            'date_label' => $formData['date_label'],
            'time' => $formData['time'],
            'booking_type' => $formData['booking_type'],
            'services' => array_values($services),
            'totals' => $totals,
            'deposit' => $deposit,
            'district_name' => $formData['district_name'] ?? null,
            'status' => 'confirmed',
            'created_at_label' => now()->setTimezone('Asia/Riyadh')->format('d/m/Y H:i'),
        ];
    }

    /**
     * Generate a unique booking reference code (e.g., #HL-4821).
     */
    public function generateRefCode(): string
    {
        return '#HL-'.str_pad((string) random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Validate the booking form data fields.
     *
     * @param  array{client_name: string, client_phone: string, service_ids: string[]}  $data
     * @return array{is_valid: bool, errors: array<string, string>}
     */
    public function validateBookingForm(array $data): array
    {
        $errors = [];

        if (empty($data['service_ids'])) {
            $errors['services'] = 'يرجى اختيار خدمة واحدة على الأقل قبل تأكيد الحجز.';
        }

        $name = trim($data['client_name'] ?? '');
        if (mb_strlen($name) < 3) {
            $errors['client_name'] = 'يرجى إدخال الاسم الكريم بشكل صحيح (3 أحرف على الأقل).';
        }

        $phone = trim($data['client_phone'] ?? '');
        if (! preg_match('/^(05\d{8}|\d{9,10})$/', $phone)) {
            $errors['client_phone'] = 'يرجى إدخال رقم جوال صحيح مكون من 10 أرقام (مثال: 0551234567).';
        }

        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
        ];
    }
}
