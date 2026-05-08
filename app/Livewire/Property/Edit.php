<?php

namespace App\Livewire\Property;

use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use App\Models\Property;
use App\Models\PropertyPhoto;
use App\Models\Settlement;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public Property $property;

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
    public array $newPhotos = [];

    /** @var string[] */
    public array $deletedExistingPhotoIds = [];

    public function mount(Property $property): void
    {
        abort_unless($property->user_id === auth()->id(), 403);

        $this->property = $property;

        $this->title = $property->title;
        $this->description = $property->description;
        $this->property_type = $property->property_type->value;
        $this->settlement_id = $property->settlement_id;
        $this->settlementQuery = $property->settlement
            ? ($property->settlement->region
                ? "{$property->settlement->name}, {$property->settlement->region}"
                : $property->settlement->name)
            : '';
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
            'settlement_id' => ['required', 'integer', 'exists:settlements,id'],
            'address' => ['required', 'string', 'max:255'],
            'rooms_count' => ['required', 'integer', 'min:1', 'max:20'],
            'area' => ['required', 'numeric', 'min:1'],
            'price_per_night' => ['required', 'numeric', 'min:0'],
            'min_nights' => ['required', 'integer', 'min:1'],
            'newPhotos' => ['nullable', 'array', 'max:7'],
            'newPhotos.*' => ['image', 'max:5120'],
        ];
    }

    public function updatedNewPhotos(): void
    {
        if (count($this->newPhotos) > 7) {
            $this->newPhotos = array_slice($this->newPhotos, 0, 7);
        }

        $this->validateOnly('newPhotos');
    }

    public function markExistingPhotoForDelete(string $photoId): void
    {
        $photo = PropertyPhoto::query()
            ->where('id', $photoId)
            ->where('property_id', $this->property->id)
            ->first();

        if (! $photo) {
            return;
        }

        if (! in_array($photoId, $this->deletedExistingPhotoIds, true)) {
            $this->deletedExistingPhotoIds[] = $photoId;
        }

        $this->dispatch(
            'existing-photos-count-updated',
            count: $this->property->photos()->whereNotIn('id', $this->deletedExistingPhotoIds)->count()
        );
    }

    public function update(): void
    {
        $this->validate();

        $existingPhotosCount = $this->property->photos()
            ->whereNotIn('id', $this->deletedExistingPhotoIds)
            ->count();

        if (($existingPhotosCount + count($this->newPhotos)) > 7) {
            $this->addError('newPhotos', 'Максимум 7 фото для одного оголошення.');

            return;
        }

        $this->property->update([
            'title' => $this->title,
            'description' => $this->description,
            'property_type' => PropertyType::from($this->property_type),
            'settlement_id' => $this->settlement_id,
            'address' => $this->address,
            'rooms_count' => $this->rooms_count,
            'area' => $this->area,
            'price_per_night' => $this->price_per_night,
            'min_nights' => $this->min_nights,

            // после редактирования снова на модерацию
            'status' => PropertyStatus::Pending,
        ]);

        if ($this->deletedExistingPhotoIds !== []) {
            $photosToDelete = PropertyPhoto::query()
                ->where('property_id', $this->property->id)
                ->whereIn('id', $this->deletedExistingPhotoIds)
                ->get();

            foreach ($photosToDelete as $photo) {
                if (! str_starts_with($photo->url, 'http://') && ! str_starts_with($photo->url, 'https://')) {
                    Storage::disk('public')->delete($photo->url);
                }

                $photo->delete();
            }
        }

        foreach ($this->newPhotos as $index => $photo) {
            $filename = time() . '_edit_' . $index . '_' . uniqid() . '.' . $photo->extension();
            $path = $photo->storeAs('properties', $filename, 'public');

            $this->property->photos()->create([
                'url' => $path,
                'is_main' => false,
                'position' => 0,
            ]);
        }

        $this->refreshMainPhotoAndPositions();

        session()->flash('status', 'Оголошення оновлено та відправлено на модерацію.');

        $this->redirect(route('profile.my-properties'));
    }

    private function refreshMainPhotoAndPositions(): void
    {
        $photos = $this->property->photos()->orderBy('created_at')->get();

        foreach ($photos as $index => $photo) {
            $photo->update([
                'is_main' => $index === 0,
                'position' => $index,
            ]);
        }
    }

    public function render()
    {
        $settlementSuggestions = collect();

        if (mb_strlen(trim($this->settlementQuery)) >= 2) {
            $settlementSuggestions = Settlement::query()
                ->where('name', 'like', '%' . trim($this->settlementQuery) . '%')
                ->orderBy('name')
                ->limit(8)
                ->get();
        }

        return view('livewire.property.edit', [
            'propertyTypes' => PropertyType::options(),
            'settlementSuggestions' => $settlementSuggestions,
        ]);
    }

    public function updatedSettlementQuery(): void
    {
        $this->settlement_id = null;
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
            'description.required' => 'Введіть опис оголошення.',
            'description.min' => 'Опис має містити щонайменше 20 символів.',
            'settlement_id.required' => 'Оберіть населений пункт зі списку.',
            'address.required' => 'Введіть адресу.',
            'rooms_count.required' => 'Вкажіть кількість кімнат.',
            'area.required' => 'Вкажіть площу.',
            'price_per_night.required' => 'Вкажіть ціну за ніч.',
            'min_nights.required' => 'Вкажіть мінімальну кількість ночей.',
        ];
    }
}
