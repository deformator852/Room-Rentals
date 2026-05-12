<?php

namespace App\Livewire\Property;

use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use App\Models\Favorite;
use App\Models\Property;
use App\Models\Settlement;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Catalog extends Component
{
    use WithPagination;

    public ?int $settlementId = null;
    public string $settlementQuery = '';
    public string $propertyType = '';
    public ?int $priceMin = null;
    public ?int $priceMax = null;
    public ?int $roomsCount = null;
    public ?string $availableFrom = null;
    public ?string $availableTo = null;
    public string $sort = 'rating_desc';

    #[Url(except: null)]
    public ?int $appliedSettlementId = null;

    #[Url(except: '')]
    public string $appliedPropertyType = '';

    #[Url(except: null)]
    public ?int $appliedPriceMin = null;

    #[Url(except: null)]
    public ?int $appliedPriceMax = null;

    #[Url(except: null)]
    public ?int $appliedRoomsCount = null;

    #[Url(except: null)]
    public ?string $appliedAvailableFrom = null;

    #[Url(except: null)]
    public ?string $appliedAvailableTo = null;

    #[Url(except: 'rating_desc')]
    public string $appliedSort = 'rating_desc';

    public function mount(): void
    {
        $this->settlementId = $this->appliedSettlementId;
        $this->propertyType = $this->appliedPropertyType;
        $this->priceMin = $this->appliedPriceMin;
        $this->priceMax = $this->appliedPriceMax;
        $this->roomsCount = $this->appliedRoomsCount;
        $this->availableFrom = $this->appliedAvailableFrom;
        $this->availableTo = $this->appliedAvailableTo;
        $this->sort = $this->appliedSort;

        if ($this->settlementId) {
            $settlement = Settlement::query()->find($this->settlementId);
            if ($settlement) {
                $this->settlementQuery = $settlement->region
                    ? "{$settlement->name}, {$settlement->region}"
                    : $settlement->name;
            }
        }
    }

    public function applyCatalogFilters(): void
    {
        $this->appliedSettlementId = $this->settlementId;
        $this->appliedPropertyType = $this->propertyType;
        $this->appliedPriceMin = $this->priceMin;
        $this->appliedPriceMax = $this->priceMax;
        $this->appliedRoomsCount = $this->roomsCount;
        $this->appliedAvailableFrom = $this->availableFrom;
        $this->appliedAvailableTo = $this->availableTo;
        $this->appliedSort = $this->sort;

        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->settlementId = null;
        $this->settlementQuery = '';
        $this->propertyType = '';
        $this->priceMin = null;
        $this->priceMax = null;
        $this->roomsCount = null;
        $this->availableFrom = null;
        $this->availableTo = null;
        $this->sort = 'rating_desc';

        $this->appliedSettlementId = null;
        $this->appliedPropertyType = '';
        $this->appliedPriceMin = null;
        $this->appliedPriceMax = null;
        $this->appliedRoomsCount = null;
        $this->appliedAvailableFrom = null;
        $this->appliedAvailableTo = null;
        $this->appliedSort = 'rating_desc';

        $this->resetPage();
    }

    public function render(): View
    {
        $propertiesQuery = Property::query()
            ->with(['mainPhoto', 'photos', 'settlement'])
            ->where('status', PropertyStatus::Published);

        $this->applyFilters($propertiesQuery);
        $this->applySorting($propertiesQuery);

        $properties = $propertiesQuery->paginate(9);
        $favoritePropertyIds = auth()->check()
            ? Favorite::query()
                ->where('user_id', auth()->id())
                ->whereIn('property_id', $properties->pluck('id'))
                ->pluck('property_id')
                ->all()
            : [];

        $settlementSuggestions = collect();

        if (mb_strlen(trim($this->settlementQuery)) >= 2) {
            $settlementSuggestions = Settlement::query()
                ->where('name', 'like', '%' . trim($this->settlementQuery) . '%')
                ->orderBy('name')
                ->limit(8)
                ->get();
        }

        return view('livewire.property.catalog', [
            'properties' => $properties,
            'propertyTypes' => PropertyType::options(),
            'settlementSuggestions' => $settlementSuggestions,
            'favoritePropertyIds' => $favoritePropertyIds,
        ]);
    }

    public function updatedSettlementQuery(): void
    {
        $this->settlementId = null;
    }

    public function selectSettlement(int $settlementId): void
    {
        $settlement = Settlement::query()->find($settlementId);

        if (! $settlement) {
            return;
        }

        $this->settlementQuery = $settlement->region
            ? "{$settlement->name}, {$settlement->region}"
            : $settlement->name;
        $this->settlementId = $settlement->id;
    }

    private function applyFilters(Builder $query): void
    {
        if ($this->appliedSettlementId !== null) {
            $query->where('settlement_id', $this->appliedSettlementId);
        }

        if ($this->appliedPropertyType !== '') {
            $query->where('property_type', $this->appliedPropertyType);
        }

        if ($this->appliedPriceMin !== null && $this->appliedPriceMin > 0) {
            $query->where('price_per_night', '>=', $this->appliedPriceMin);
        }

        if ($this->appliedPriceMax !== null && $this->appliedPriceMax > 0) {
            $query->where('price_per_night', '<=', $this->appliedPriceMax);
        }

        if ($this->appliedRoomsCount !== null && $this->appliedRoomsCount > 0) {
            $query->where('rooms_count', $this->appliedRoomsCount);
        }

        if ($this->appliedAvailableFrom && $this->appliedAvailableTo) {
            try {
                $from = Carbon::parse($this->appliedAvailableFrom)->startOfDay();
                $to = Carbon::parse($this->appliedAvailableTo)->startOfDay();

                if ($to->greaterThan($from)) {
                    $query->whereDoesntHave('bookings', function (Builder $bookingQuery) use ($from, $to) {
                        $bookingQuery
                            ->whereIn('status', ['pending', 'confirmed'])
                            ->where('check_in', '<', $to->toDateString())
                            ->where('check_out', '>', $from->toDateString());
                    });
                }
            } catch (\Throwable) {
                // Ignore invalid date values from query string.
            }
        }
    }

    private function applySorting(Builder $query): void
    {
        match ($this->appliedSort) {
            'price_asc' => $query
                ->orderBy('price_per_night')
                ->orderByDesc('avg_rating')
                ->orderByDesc('reviews_count'),
            'price_desc' => $query
                ->orderByDesc('price_per_night')
                ->orderByDesc('avg_rating')
                ->orderByDesc('reviews_count'),
            'rating_asc' => $query
                ->orderBy('avg_rating')
                ->orderBy('reviews_count')
                ->orderBy('price_per_night'),
            default => $query
                ->orderByDesc('avg_rating')
                ->orderByDesc('reviews_count')
                ->orderByDesc('price_per_night'),
        };

        $query->orderByDesc('created_at');
    }
}
