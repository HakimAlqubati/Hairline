<?php

declare(strict_types=1);

namespace App\Services\FrontData;

/**
 * Provides static customer account data for the demo frontend.
 *
 * In a real implementation, this service would delegate to a repository
 * backed by a database. For now, it provides realistic demo profiles
 * that can be "logged into" via a customer code or phone number.
 */
class CustomerAccountDataService
{
    /**
     * Demo accounts — indexed by customer_code for quick lookup.
     *
     * @return array<string, array{
     *     customer_code: string,
     *     name: string,
     *     phone: string,
     *     member_since: string,
     *     tier: string,
     *     tier_color: string,
     *     points: int
     * }>
     */
    private function getDemoAccounts(): array
    {
        return [
            'HL-1001' => [
                'customer_code' => 'HL-1001',
                'name' => 'سارة العتيبي',
                'phone' => '0551234567',
                'member_since' => '2024-03',
                'tier' => 'Gold',
                'tier_color' => '#C5A373',
                'points' => 2_450,
            ],
            'HL-2002' => [
                'customer_code' => 'HL-2002',
                'name' => 'نورة الشمري',
                'phone' => '0559876543',
                'member_since' => '2025-01',
                'tier' => 'Silver',
                'tier_color' => '#9CA3AF',
                'points' => 980,
            ],
        ];
    }

    /**
     * Attempt login by customer code or phone number (case-insensitive).
     *
     * @return array{
     *     success: bool,
     *     customer: array<string, mixed>|null,
     *     message: string
     * }
     */
    public function login(string $identifier): array
    {
        $identifier = strtoupper(trim($identifier));

        // Try code first
        if (isset($this->getDemoAccounts()[$identifier])) {
            return [
                'success' => true,
                'customer' => $this->getDemoAccounts()[$identifier],
                'message' => 'أهلاً بعودتكِ! تم تسجيل الدخول بنجاح.',
            ];
        }

        // Try by phone number
        foreach ($this->getDemoAccounts() as $account) {
            if ($account['phone'] === $identifier) {
                return [
                    'success' => true,
                    'customer' => $account,
                    'message' => 'أهلاً بعودتكِ! تم تسجيل الدخول بنجاح.',
                ];
            }
        }

        return [
            'success' => false,
            'customer' => null,
            'message' => 'لم يتم العثور على الحساب. تأكدي من كود العميلة أو رقم الجوال.',
        ];
    }

    /**
     * Get the booking history for a customer code.
     *
     * @return array<int, array{
     *     ref_code: string,
     *     date_label: string,
     *     time: string,
     *     services: string[],
     *     total_sar: int,
     *     status: string,
     *     status_label_ar: string,
     *     booking_type: string
     * }>
     */
    public function getBookingHistory(string $customerCode): array
    {
        $historyMap = [
            'HL-1001' => [
                [
                    'ref_code' => '#HL-4281',
                    'date_label' => 'الثلاثاء، 8 أكتوبر 2026',
                    'time' => '04:00 م',
                    'services' => ['سشوار وويفي هيرلاين الفاخر', 'مانيكير روسي دقيق'],
                    'total_sar' => 310,
                    'status' => 'completed',
                    'status_label_ar' => 'مكتمل',
                    'booking_type' => 'salon',
                ],
                [
                    'ref_code' => '#HL-3197',
                    'date_label' => 'السبت، 28 سبتمبر 2026',
                    'time' => '05:30 م',
                    'services' => ['تسريحة مناسبات وسهرة ملكية'],
                    'total_sar' => 350,
                    'status' => 'completed',
                    'status_label_ar' => 'مكتمل',
                    'booking_type' => 'salon',
                ],
                [
                    'ref_code' => '#HL-5022',
                    'date_label' => 'الجمعة، 18 أكتوبر 2026',
                    'time' => '07:00 م',
                    'services' => ['صبغة جذور ولون كامل باللمعة الحريرية'],
                    'total_sar' => 480,
                    'status' => 'upcoming',
                    'status_label_ar' => 'قادم',
                    'booking_type' => 'salon',
                ],
            ],
            'HL-2002' => [
                [
                    'ref_code' => '#HL-6611',
                    'date_label' => 'الاثنين، 13 أكتوبر 2026',
                    'time' => '02:30 م',
                    'services' => ['تنظيف بشرة عميق (هيدرافيشل الملكي)'],
                    'total_sar' => 380,
                    'status' => 'upcoming',
                    'status_label_ar' => 'قادم',
                    'booking_type' => 'home',
                ],
            ],
        ];

        return $historyMap[$customerCode] ?? [];
    }

    /**
     * Get all invoices for a customer code.
     *
     * @return array<int, array{
     *     invoice_no: string,
     *     date: string,
     *     ref_code: string,
     *     items: array<int, array{name: string, price: int}>,
     *     subtotal: int,
     *     visit_fee: int,
     *     total: int,
     *     paid: bool
     * }>
     */
    public function getInvoices(string $customerCode): array
    {
        $invoiceMap = [
            'HL-1001' => [
                [
                    'invoice_no' => 'INV-2026-0841',
                    'date' => '08/10/2026',
                    'ref_code' => '#HL-4281',
                    'items' => [
                        ['name' => 'سشوار وويفي هيرلاين الفاخر', 'price' => 160],
                        ['name' => 'مانيكير روسي دقيق',          'price' => 150],
                    ],
                    'subtotal' => 310,
                    'visit_fee' => 0,
                    'total' => 310,
                    'paid' => true,
                ],
                [
                    'invoice_no' => 'INV-2026-0712',
                    'date' => '28/09/2026',
                    'ref_code' => '#HL-3197',
                    'items' => [
                        ['name' => 'تسريحة مناسبات وسهرة ملكية', 'price' => 350],
                    ],
                    'subtotal' => 350,
                    'visit_fee' => 0,
                    'total' => 350,
                    'paid' => true,
                ],
            ],
        ];

        return $invoiceMap[$customerCode] ?? [];
    }
}
