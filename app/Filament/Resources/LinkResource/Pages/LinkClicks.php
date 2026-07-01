<?php

namespace App\Filament\Resources\LinkResource\Pages;

use App\Filament\Resources\LinkResource;
use App\Models\Click;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LinkClicks extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = LinkResource::class;

    protected static string $view = 'filament.resources.link-resource.pages.link-clicks';

    public ?\App\Models\Link $link = null;

    public function mount(int|string $record): void
    {
        $this->link = LinkResource::getEloquentQuery()->findOrFail($record);
    }

    public function getTitle(): string
    {
        return 'Статистика: ' . $this->link->short_url;
    }

    protected function getTableQuery(): Builder
    {
        return Click::query()->where('link_id', $this->link->id);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('ip_address')->label('IP')->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата/время перехода')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('referer')
                    ->label('Referer')
                    ->limit(40)
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('user_agent')
                    ->label('User-Agent')
                    ->limit(40)
                    ->tooltip(fn ($state) => $state)
                    ->placeholder('—'),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
