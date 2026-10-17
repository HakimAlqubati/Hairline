<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Services\FrontData\CatalogDataService;
use App\Services\FrontData\SalonInfoDataService;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Home page component — displays the two-word hero, main navigation rows,
 * interactive services showcase, and the hero portrait slider.
 */
#[Layout('layouts.app')]
#[Title('HAIRLINE BEAUTY LOUNGE | صالون هيرلاين النسائي')]
class Home extends Component
{
    public int $currentHeroSlide = 0;

    /** @var array<string, mixed> */
    public array $salonIdentity = [];

    /** @var array<int, array<string, mixed>> */
    public array $categories = [];

    /** @var array<int, array<string, mixed>> */
    public array $featuredServices = [];

    /** @var array<int, array<string, mixed>> */
    public array $lengthsGuide = [];

    /** @var array<int, array<string, mixed>> */
    public array $gallery = [];

    public function mount(SalonInfoDataService $salonInfo, CatalogDataService $catalog): void
    {
        $this->salonIdentity = $salonInfo->getSalonIdentity();
        $this->categories = $catalog->getAllCategories();
        $this->featuredServices = $catalog->getFeaturedServices();
        $this->lengthsGuide = $catalog->getHairLengthsGuide();
        $this->gallery = $salonInfo->getSalonGallery();
    }

    public function switchSlide(int $index): void
    {
        $this->currentHeroSlide = $index;
    }

    public function render(): View
    {
        return view('livewire.pages.home');
    }
}
