<?php

namespace App\Filament\Resources\Properties\Pages;

use App\Filament\Resources\Properties\PropertyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProperty extends CreateRecord
{
    protected static string $resource = PropertyResource::class;

    protected function afterCreate(): void
    {
        $data = $this->form->getRawState();

        if (!empty($data['images'])) {
            foreach ($data['images'] as $index => $path) {
                $this->record->media()->create([
                    'file_path' => $path,
                    'file_type' => 'image/' . pathinfo($path, PATHINFO_EXTENSION),
                    'is_primary' => $index === 0 ? 1 : 0, // Ensure tinyint(1) compatibility
                ]);
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Property created successfully';
    }
}
