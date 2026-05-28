<?php

namespace App\Filament\Resources\Properties\Pages;

use App\Filament\Resources\Properties\PropertyResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditProperty extends EditRecord
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    // Pulls existing paths into the uploader
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['images'] = $this->record->media()->pluck('file_path')->toArray();
        return $data;
    }

    protected function afterSave(): void
    {
        $data = $this->form->getRawState();

        if (isset($data['images'])) {
            // Clear old database records for this property
            $this->record->media()->delete();

            foreach ($data['images'] as $index => $path) {
                $this->record->media()->create([
                    'file_path' => $path,
                    'file_type' => 'image/' . pathinfo($path, PATHINFO_EXTENSION),
                    'is_primary' => $index === 0 ? 1 : 0,
                ]);
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Property updated successfully';
    }
}
