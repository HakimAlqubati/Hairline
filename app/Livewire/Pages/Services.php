<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Services\FrontData\CatalogDataService;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Services catalog page — hierarchical accordion (category → sub-services)
 * with a cart bag, search, and "احجزي الآن" direct-to-booking shortcuts.
 */
#[Layout('layouts.app')]
#[Title('الخدمات | HAIRLINE BEAUTY LOUNGE')]
class Services extends Component
{
    #[Url(as: 'q')]
    public string $searchQuery = '';

    #[Url(as: 'cat')]
    public ?string $cat = null;

    public ?string $openCategoryId = null;

    /** @var string[] */
    public array $selectedServiceIds = [];

    /** @var array<int, array<string, mixed>> */
    public array $categories = [];

    public bool $showLengthsGuide = false;

    public function mount(CatalogDataService $catalog): void
    {
        $this->categories = $catalog->getAllCategories();

        if ($this->cat !== null) {
            $this->openCategoryId = $this->cat;
        } elseif (! empty($this->categories)) {
            $this->openCategoryId = $this->categories[0]['id'];
        }
    }

    public function toggleLengthsGuide(): void
    {
        $this->showLengthsGuide = ! $this->showLengthsGuide;
    }

    public function toggleCategory(string $categoryId): void
    {
        $this->openCategoryId = ($this->openCategoryId === $categoryId)
            ? null
            : $categoryId;
    }

    public function toggleService(string $serviceId): void
    {
        if (in_array($serviceId, $this->selectedServiceIds, true)) {
            $this->selectedServiceIds = array_values(
                array_filter($this->selectedServiceIds, fn (string $id) => $id !== $serviceId)
            );
        } else {
            $this->selectedServiceIds[] = $serviceId;
        }
    }

    public function directBook(string $categoryId, string $serviceId): void
    {
        if (! in_array($serviceId, $this->selectedServiceIds, true)) {
            $this->selectedServiceIds[] = $serviceId;
        }

        // Pass selected services to booking via session
        session(['booking.selected_services' => $this->selectedServiceIds]);
        session(['booking.active_category' => $categoryId]);

        $this->redirectRoute('booking', navigate: true);
    }

    public function proceedToBooking(): void
    {
        session(['booking.selected_services' => $this->selectedServiceIds]);
        $this->redirectRoute('booking', navigate: true);
    }

    /** @return array<int, array<string, mixed>> */
    public function getFilteredCategoriesProperty(CatalogDataService $catalog): array
    {
        if (empty(trim($this->searchQuery))) {
            return $this->categories;
        }

        $results = $catalog->searchServices($this->searchQuery);
        if (empty($results)) {
            return [];
        }

        // Rebuild category structure from search results
        $grouped = [];
        foreach ($results as $result) {
            $catId = $result['category_id'];
            if (! isset($grouped[$catId])) {
                foreach ($this->categories as $cat) {
                    if ($cat['id'] === $catId) {
                        $grouped[$catId] = array_merge($cat, ['services' => []]);
                        break;
                    }
                }
            }
            $grouped[$catId]['services'][] = $result['service'];
        }

        return array_values($grouped);
    }

    public function calculateTotals(CatalogDataService $catalog): array
    {
        return $catalog->calculateTotals($this->selectedServiceIds);
    }

    public function render(CatalogDataService $catalog): View
    {
        return view('livewire.pages.services', [
            'filteredCategories' => $this->getFilteredCategoriesProperty($catalog),
            'totals' => $catalog->calculateTotals($this->selectedServiceIds),
            'lengthsGuide' => $catalog->getHairLengthsGuide(),
        ]);
    }
}
