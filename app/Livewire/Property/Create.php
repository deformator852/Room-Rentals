<?php

namespace App\Livewire\Property;

use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public string $title = '';

    public string $description = '';

    public string $property_type = 'apartment';

    public string $city = '';

    public string $address = '';

    public ?int $rooms_count = null;

    public ?float $area = null;

    public ?float $price_per_night = null;

    public int $min_nights = 1;

    /** @var TemporaryUploadedFile[] */
    public array $photos = [];

    public function removePhoto(int $index): void
    {
        array_splice($this->photos, $index, 1);
    }

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:5', 'max:100'],
            'description' => ['required', 'string', 'min:20', 'max:2000'],
            'property_type' => ['required', 'string'],
            'city' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:255'],
            'rooms_count' => ['required', 'integer', 'min:1', 'max:20'],
            'area' => ['required', 'numeric', 'min:1'],
            'price_per_night' => ['required', 'numeric', 'min:0'],
            'min_nights' => ['required', 'integer', 'min:1'],
            'photos' => ['nullable', 'array', 'max:10'],
            'photos.*' => ['image', 'max:5120'],
        ];
    }

    public function saveDraft(): void
    {
        $this->validate([
            'title' => ['required', 'string', 'min:5', 'max:100'],
        ]);

        $this->storeProperty(PropertyStatus::Draft);

        session()->flash('status', 'Чернетку збережено.');
        $this->redirect(route('properties.index'));
    }

    public function publish(): void
    {
        $this->validate();

        $this->storeProperty(PropertyStatus::Pending);

        session()->flash('status', 'Оголошення відправлено на модерацію.');
        $this->redirect(route('properties.index'));
    }

    private function storeProperty(PropertyStatus $status): void
    {
        $property = Auth::user()->properties()->create([
            'title' => $this->title,
            'description' => $this->description,
            'property_type' => PropertyType::from($this->property_type),
            'city' => $this->city,
            'address' => $this->address,
            'rooms_count' => $this->rooms_count,
            'area' => $this->area,
            'price_per_night' => $this->price_per_night,
            'min_nights' => $this->min_nights,
            'status' => $status,
        ]);

        foreach ($this->photos as $i => $photo) {
            $path = $photo->store('properties', 'public');
            $property->photos()->create([
                'path' => $path,
                'is_main' => $i === 0,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.property.create');
    }
}
