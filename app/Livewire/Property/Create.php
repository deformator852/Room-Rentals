<?php

namespace App\Livewire\Property;

use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use App\Models\Settlement;
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

    public ?int $settlement_id = null;

    public string $settlementQuery = '';

    public string $address = '';

    public ?int $rooms_count = null;

    public ?float $area = null;

    public ?float $price_per_night = null;

    public int $min_nights = 1;

    /** @var TemporaryUploadedFile[] */
    public array $photos = [];

    public function updatedPhotos(): void
    {
        if (count($this->photos) > 7) {
            $this->photos = array_slice($this->photos, 0, 7);
        }

        $this->validateOnly('photos');
    }

    public function removePhoto(int $index): void
    {
        array_splice($this->photos, $index, 1);
    }

    public function updatedSettlementQuery(): void
    {
        $this->settlement_id = null;
    }

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:5', 'max:100'],
            'description' => ['required', 'string', 'min:20', 'max:2000'],
            'property_type' => ['required', 'string'],
            'settlement_id' => ['required', 'integer', 'exists:settlements,id'],
            'address' => ['required', 'string', 'max:255'],
            'rooms_count' => ['required', 'integer', 'min:1', 'max:20'],
            'area' => ['required', 'numeric', 'min:1'],
            'price_per_night' => ['required', 'numeric', 'min:0'],
            'min_nights' => ['required', 'integer', 'min:1'],
            'photos' => ['nullable', 'array', 'max:7'],
            'photos.*' => ['image', 'max:5120'],
        ];
    }

    public function saveDraft(): void
    {
        $this->validate([
            'title' => ['required', 'string', 'min:5', 'max:100'],
            'property_type' => ['required', 'string'],
            'settlement_id' => ['required', 'integer', 'exists:settlements,id'],
        ]);

        $this->storeProperty(PropertyStatus::Draft);

        session()->flash('status', 'Чернетку збережено.');
        $this->redirect(route('home'));
    }

    public function publish(): void
    {
        $this->validate();
        $this->storeProperty(PropertyStatus::Pending);

        session()->flash('status', 'Оголошення відправлено на модерацію.');
        $this->redirect(route('home'));
    }

    private function storeProperty(PropertyStatus $status): void
    {
        $property = Auth::user()->properties()->create([
            'title' => $this->title,
            'description' => $this->description,
            'property_type' => PropertyType::from($this->property_type),
            'settlement_id' => $this->settlement_id,
            'address' => $this->address,
            'rooms_count' => $this->rooms_count,
            'area' => $this->area,
            'price_per_night' => $this->price_per_night,
            'min_nights' => $this->min_nights,
            'status' => $status,
        ]);

        foreach ($this->photos as $i => $photo) {
            $filename = time() . '_' . $i . '_' . uniqid() . '.' . $photo->extension();
            $path = $photo->storeAs('properties', $filename, 'public');

            $property->photos()->create([
                'url' => $path,
                'is_main' => $i === 0,
            ]);
        }
    }

    public function render()
    {
        $propertyTypes = PropertyType::options();
        $settlementSuggestions = collect();

        if (mb_strlen(trim($this->settlementQuery)) >= 2) {
            $settlementSuggestions = Settlement::query()
                ->where('name', 'like', '%' . trim($this->settlementQuery) . '%')
                ->orderBy('name')
                ->limit(8)
                ->get();
        }

        return view('livewire.property.create', [
            'propertyTypes' => $propertyTypes,
            'settlementSuggestions' => $settlementSuggestions,
        ]);
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
        $this->settlement_id = $settlement->id;
    }

    protected function messages(): array
    {
        return [
            'title.required' => 'Введіть назву оголошення.',
            'title.min' => 'Назва має містити щонайменше 5 символів.',
            'title.max' => 'Назва не може бути довшою за 100 символів.',

            'description.required' => 'Введіть опис оголошення.',
            'description.min' => 'Опис має містити щонайменше 20 символів.',
            'description.max' => 'Опис не може бути довшим за 2000 символів.',

            'property_type.required' => 'Оберіть тип нерухомості.',

            'settlement_id.required' => 'Оберіть населений пункт зі списку.',
            'settlement_id.exists' => 'Оберіть коректний населений пункт зі списку.',

            'address.required' => 'Введіть адресу.',
            'address.max' => 'Адреса не може бути довшою за 255 символів.',

            'rooms_count.required' => 'Вкажіть кількість кімнат.',
            'rooms_count.integer' => 'Кількість кімнат має бути цілим числом.',
            'rooms_count.min' => 'Кількість кімнат має бути не менше 1.',
            'rooms_count.max' => 'Кількість кімнат не може бути більше 20.',

            'area.required' => 'Вкажіть площу.',
            'area.numeric' => 'Площа має бути числом.',
            'area.min' => 'Площа має бути більшою за 0.',

            'price_per_night.required' => 'Вкажіть ціну за ніч.',
            'price_per_night.numeric' => 'Ціна має бути числом.',
            'price_per_night.min' => 'Ціна не може бути від’ємною.',

            'min_nights.required' => 'Вкажіть мінімальну кількість ночей.',
            'min_nights.integer' => 'Кількість ночей має бути цілим числом.',
            'min_nights.min' => 'Мінімальна кількість ночей має бути не менше 1.',

            'photos.array' => 'Фото мають бути передані списком.',
            'photos.max' => 'Можна завантажити не більше 7 фото.',

            'photos.*.image' => 'Кожен файл має бути зображенням.',
            'photos.*.max' => 'Розмір кожного фото не має перевищувати 5 МБ.',
        ];
    }
}
