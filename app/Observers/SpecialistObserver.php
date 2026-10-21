<?php

declare(strict_types=1);

namespace App\Observers;

use App\Services\Cache\HairlineCacheService;

class SpecialistObserver
{
    public function saved(): void
    {
        HairlineCacheService::clearSpecialists();
    }

    public function deleted(): void
    {
        HairlineCacheService::clearSpecialists();
    }
}
