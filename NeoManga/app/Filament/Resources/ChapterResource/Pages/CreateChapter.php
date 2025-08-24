<?php
namespace App\Filament\Resources\ChapterResource\Pages;

use App\Filament\Resources\ChapterResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateChapter extends CreateRecord
{
    protected static string $resource = ChapterResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-generate slug if not provided
        if (empty($data['slug']) && !empty($data['manga_id']) && !empty($data['number'])) {
            $manga = \App\Models\Manga::find($data['manga_id']);
            if ($manga) {
                $data['slug'] = Str::slug($manga->title . '-chapter-' . $data['number']);
            }
        }

        return $data;
    }
}