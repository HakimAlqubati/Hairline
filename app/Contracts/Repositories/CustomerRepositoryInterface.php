<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Models\Customer;

interface CustomerRepositoryInterface
{
    /**
     * Find an active customer by primary key ID.
     */
    public function findById(int $id): ?Customer;

    /**
     * Find a customer by normalized Saudi mobile phone number (05XXXXXXXX).
     */
    public function findByPhone(string $normalizedPhone): ?Customer;

    /**
     * Register a new customer or update an existing customer's name after SMS OTP verification.
     */
    public function registerOrUpdateByPhone(string $normalizedPhone, string $name): Customer;
}
