<?php

namespace App\Filament\Resources\LinkResource\Pages;

use App\Filament\Resources\LinkResource;
use App\Filament\Resources\LinkResource\Widgets\LinksOverview;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLinks extends ListRecords
{
    protected static string $resource = LinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Создать ссылку'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            LinksOverview::class,
        ];
    }
}
