<?php

namespace App\Filament\Resources\MangaResource\Pages;

use App\Filament\Resources\MangaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListMangas extends ListRecords
{
    protected static string $resource = MangaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Manga')
                ->badge(fn () => $this->getModel()::count()),
            
            'ongoing' => Tab::make('Ongoing')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'ongoing'))
                ->badge(fn () => $this->getModel()::where('status', 'ongoing')->count()),
            
            'completed' => Tab::make('Completed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'completed'))
                ->badge(fn () => $this->getModel()::where('status', 'completed')->count()),
            
            'hiatus' => Tab::make('Hiatus')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'hiatus'))
                ->badge(fn () => $this->getModel()::where('status', 'hiatus')->count()),
            
        ];
    }
}