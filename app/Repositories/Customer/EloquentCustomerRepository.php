<?php

declare(strict_types=1);

namespace App\Repositories\Customer;

use App\Contracts\Repositories\CustomerRepositoryInterface;
use App\Models\Customer;
use App\Models\MembershipTier;
use Illuminate\Support\Facades\DB;

class EloquentCustomerRepository implements CustomerRepositoryInterface
{
    public function findById(int $id): ?Customer
    {
        return Customer::find($id);
    }

    public function findByPhone(string $normalizedPhone): ?Customer
    {
        return Customer::where('phone', $normalizedPhone)->first();
    }

    public function registerOrUpdateByPhone(string $normalizedPhone, string $name): Customer
    {
        $cleanName = trim($name);

        return DB::transaction(function () use ($normalizedPhone, $cleanName): Customer {
            $customer = Customer::where('phone', $normalizedPhone)->lockForUpdate()->first();

            if ($customer) {
                $updates = ['is_active' => true];

                if ($cleanName !== '' && $customer->name !== $cleanName) {
                    $updates['name'] = $cleanName;
                }

                $customer->update($updates);

                return $customer->fresh() ?? $customer;
            }

            $defaultTierId = MembershipTier::where('slug', 'silver')->value('id')
                ?? MembershipTier::orderBy('min_points_required')->value('id');

            do {
                $customerCode = 'HL-'.random_int(1000, 9999);
            } while (Customer::where('customer_code', $customerCode)->exists());

            return Customer::create([
                'membership_tier_id' => $defaultTierId,
                'customer_code' => $customerCode,
                'name' => $cleanName,
                'phone' => $normalizedPhone,
                'email' => null,
                'avatar_url' => 'https://ui-avatars.com/api/?name='.urlencode($cleanName).'&background=FFFFFF&color=C29E6D&bold=true',
                'total_points' => 0,
                'is_active' => true,
            ]);
        });
    }
}
