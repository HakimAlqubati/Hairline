<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Models\Customer;
use App\Models\MembershipTier;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class GoogleCustomerSyncService
{
    public function __construct(
        private readonly GoogleOAuthService $googleOAuthService
    ) {}

    /**
     * Find or create the customer in the database after Google verification with strict account binding.
     *
     * @throws InvalidArgumentException
     */
    public function findOrCreateGoogleCustomer(
        string $normalizedPhone,
        string $email,
        string $ownerName,
        ?string $avatarUrl = null
    ): Customer {
        $emailTrimmed = strtolower(trim($email));
        if ($emailTrimmed === '') {
            throw new InvalidArgumentException('البريد الإلكتروني لحساب Google غير متوفر.');
        }

        return DB::transaction(function () use ($normalizedPhone, $emailTrimmed, $ownerName, $avatarUrl): Customer {
            $customerByPhone = $normalizedPhone !== '' ? Customer::where('phone', $normalizedPhone)->first() : null;
            $customerByEmail = Customer::where('email', $emailTrimmed)->first();

            // 1. Existing customer found by Google email
            if ($customerByEmail) {
                if ($normalizedPhone !== '') {
                    if (empty($customerByEmail->phone)) {
                        $customerByEmail->update(['phone' => $normalizedPhone]);
                    } elseif ($customerByEmail->phone !== $normalizedPhone) {
                        throw new InvalidArgumentException('حساب Google لا يطابق البريد الإلكتروني المسجل لهذا الرقم.');
                    }
                }

                if ($avatarUrl && empty($customerByEmail->avatar_url)) {
                    $customerByEmail->update(['avatar_url' => $avatarUrl]);
                }

                return $customerByEmail;
            }

            // 2. Existing customer found by phone number
            if ($customerByPhone) {
                if (! empty($customerByPhone->email)) {
                    if (strtolower(trim((string) $customerByPhone->email)) !== $emailTrimmed) {
                        throw new InvalidArgumentException('حساب Google لا يطابق البريد الإلكتروني المسجل لهذا الرقم.');
                    }
                } else {
                    // Customer exists by phone only (e.g. added offline); link their Google email now
                    $customerByPhone->update(['email' => $emailTrimmed]);
                }

                if ($avatarUrl && empty($customerByPhone->avatar_url)) {
                    $customerByPhone->update(['avatar_url' => $avatarUrl]);
                }

                return $customerByPhone;
            }

            // 3. New customer (neither phone nor email exists in system)
            $defaultTierId = MembershipTier::where('slug', 'silver')->value('id')
                ?? MembershipTier::orderBy('min_points_required')->value('id');

            do {
                $customerCode = 'HL-'.random_int(1000, 9999);
            } while (Customer::where('customer_code', $customerCode)->exists());

            return Customer::create([
                'membership_tier_id' => $defaultTierId,
                'customer_code' => $customerCode,
                'name' => $ownerName !== '' ? $ownerName : $this->googleOAuthService->extractNameFromEmail($emailTrimmed),
                'phone' => $normalizedPhone !== '' ? $normalizedPhone : null,
                'email' => $emailTrimmed,
                'avatar_url' => $avatarUrl ?: 'https://ui-avatars.com/api/?name='.urlencode($ownerName).'&background=211C18&color=C5A373&bold=true',
                'total_points' => 0,
                'is_active' => true,
            ]);
        });
    }
}
