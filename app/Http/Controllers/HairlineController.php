<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\FrontData\CatalogDataService;
use App\Services\FrontData\SalonInfoDataService;
use App\Services\FrontData\SpecialistsDataService;
use Illuminate\View\View;

class HairlineController extends Controller
{
    public function __construct(
        private readonly SalonInfoDataService $salonInfo,
        private readonly CatalogDataService $catalog,
        private readonly SpecialistsDataService $specialists
    ) {}

    public function show(string $initialView = 'home'): View
    {
        $identity = $this->salonInfo->getSalonIdentity();

        return view('hairline', [
            'initialView' => $initialView,
            'salonIdentity' => $identity,
            'heroSlides' => $identity['hero_slides'],
            'stats' => $identity['stats'],
            'contactLinks' => $this->salonInfo->getContactLinks(),
            'workingHours' => $this->salonInfo->getWorkingHours(),
            'location' => $this->salonInfo->getLocation(),
            'walkInServices' => $this->catalog->getWalkInServicesGrouped(),
            'bookableCategories' => $this->catalog->getBookableCategoriesWithSpecialists(),
            'specialistsData' => $this->specialists->getAllSpecialists(),
        ]);
    }
}
