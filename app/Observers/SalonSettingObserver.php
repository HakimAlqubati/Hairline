<?php

declare(strict_types=1);

namespace App\Observers;

use App\Services\Cache\HairlineCacheService;

class SalonSettingObserver
{
    public function saved(): void
    {
        HairlineCacheService::clearSalonInfo();
    }

    public function deleted(): void
    {
        HairlineCacheService::clearSalonInfo();
    }
}
