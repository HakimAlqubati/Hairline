<?php

declare(strict_types=1);

namespace App\Services\Customer;

use App\Models\Customer;
use Illuminate\Http\UploadedFile;

class CustomerProfileService
{
    /**
     * Update customer profile details and optional avatar image.
     */
    public function updateCustomer(
        Customer $customer,
        string $name,
        ?string $phone,
        ?UploadedFile $avatarFile = null
    ): Customer {
        $avatarUrl = $customer->avatar_url;
        if ($avatarFile) {
            $path = $avatarFile->store('customers', 'public');
            $avatarUrl = asset('storage/'.$path);
        }

        $customer->update([
            'name' => trim($name),
            'phone' => $phone,
            'avatar_url' => $avatarUrl,
        ]);

        session(['logged_in_customer_id' => $customer->id]);

        return $customer->fresh();
    }

    /**
     * Clear customer authentication and pending oauth states from the session.
     */
    public function logout(): void
    {
        session()->forget([
            'logged_in_customer_id',
            'pending_google_booking',
            'google_oauth_state',
        ]);
    }
}
