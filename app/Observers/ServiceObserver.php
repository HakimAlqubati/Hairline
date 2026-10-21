<?php

declare(strict_types=1);

namespace App\Observers;

use App\Services\Cache\HairlineCacheService;

class ServiceObserver
{
    public function saved(): void
    {
        HairlineCacheService::clearCatalog();
    }

    public function deleted(): void
    {
        HairlineCacheService::clearCatalog();
    }
}
