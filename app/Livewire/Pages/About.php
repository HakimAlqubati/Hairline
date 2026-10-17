<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Services\FrontData\SalonInfoDataService;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * About page — shows salon bio, working hours, location, advantages,
 * and contact links. Includes a bottom sheet for map/location quick access.
 */
#[Layout('layouts.app')]
#[Title('عن هيرلاين | HAIRLINE BEAUTY LOUNGE')]
class About extends Component
{
    public bool $showLocationSheet = false;

    /** @var array<string, mixed> */
    public array $identity = [];

    /** @var array<string, mixed> */
    public array $workingHours = [];

    /** @var array<string, mixed> */
    public array $location = [];

    /** @var array<string, mixed> */
    public array $contactLinks = [];

    /** @var array<int, array<string, string>> */
    public array $advantages = [];

    public function mount(SalonInfoDataService $salonInfo): void
    {
        $this->identity = $salonInfo->getSalonIdentity();
        $this->workingHours = $salonInfo->getWorkingHours();
        $this->location = $salonInfo->getLocation();
        $this->contactLinks = $salonInfo->getContactLinks();
        $this->advantages = $salonInfo->getCompetitiveAdvantages();
    }

    public function openLocationSheet(): void
    {
        $this->showLocationSheet = true;
    }

    public function closeLocationSheet(): void
    {
        $this->showLocationSheet = false;
    }

    public function render(): View
    {
        return view('livewire.pages.about');
    }
}
