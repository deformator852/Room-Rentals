<?php

namespace App\Livewire\Property;

use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use App\Models\Property;
use Livewire\Component;

class Edit extends Component
{
    public Property $property;

    public string $title = '';
    public string $description = '';
    public string $property_type = 'apartment';
    public string $city = '';
    public string $address = '';
    public ?int $rooms_count = null;
    public ?float $area = null;
    public ?float $price_per_night = null;
    public int $min_nights = 1;

    public function mount(Property $property): void
    {
        abort_unless($property->user_id === auth()->id(), 403);

        $this->property = $property;

        $this->title = $property->title;
        $this->description = $property->description;
        $this->property_type = $property->property_type->value;
        $this->city = $property->city;
        $this->address = $property->address;
        $this->rooms_count = $property->rooms_count;
        $this->area = $property->area;
        $this->price_per_night = (float)$property->price_per_night;
        $this->min_nights = $property->min_nights;
    }

    public function updatedPropertyType($value): void
    {
        if ($value === 'room') {
            $this->rooms_count = 1;
        }
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
        ];
    }

    public function update(): void
    {
        $this->validate();

        $this->property->update([
            'title' => $this->title,
            'description' => $this->description,
            'property_type' => PropertyType::from($this->property_type),
            'city' => $this->city,
            'address' => $this->address,
            'rooms_count' => $this->rooms_count,
            'area' => $this->area,
            'price_per_night' => $this->price_per_night,
            'min_nights' => $this->min_nights,

            // после редактирования снова на модерацию
            'status' => PropertyStatus::Pending,
        ]);

        session()->flash('status', 'Оголошення оновлено та відправлено на модерацію.');

        $this->redirect(route('profile.my-properties'));
    }

    public function render()
    {
        return view('livewire.property.edit', [
            'propertyTypes' => PropertyType::options(),
        ]);
    }

    protected function messages(): array
    {
        return [
            'title.required' => 'Введіть назву оголошення.',
            'title.min' => 'Назва має містити щонайменше 5 символів.',
            'description.required' => 'Введіть опис оголошення.',
            'description.min' => 'Опис має містити щонайменше 20 символів.',
            'city.required' => 'Введіть місто.',
            'address.required' => 'Введіть адресу.',
            'rooms_count.required' => 'Вкажіть кількість кімнат.',
            'area.required' => 'Вкажіть площу.',
            'price_per_night.required' => 'Вкажіть ціну за ніч.',
            'min_nights.required' => 'Вкажіть мінімальну кількість ночей.',
        ];
    }
}
