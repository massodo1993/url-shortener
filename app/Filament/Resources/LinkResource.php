<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LinkResource\Pages;
use App\Models\Link;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LinkResource extends Resource
{
    protected static ?string $model = Link::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?string $navigationLabel = 'Мои ссылки';

    protected static ?string $modelLabel = 'ссылка';

    protected static ?string $pluralModelLabel = 'Ссылки';

    // Пользователь видит и управляет ТОЛЬКО своими ссылками.
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('original_url')
                ->label('Оригинальный URL')
                ->required()
                ->url()
                ->maxLength(2048)
                ->columnSpanFull()
                ->placeholder('https://example.com/page'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Короткий код')
                    ->searchable()
                    ->copyable()
                    ->formatStateUsing(fn (Link $record) => $record->short_url)
                    ->copyableState(fn (Link $record) => $record->short_url),

                Tables\Columns\TextColumn::make('original_url')
                    ->label('Оригинальный URL')
                    ->limit(50)
                    ->tooltip(fn (Link $record) => $record->original_url)
                    ->url(fn (Link $record) => $record->original_url, true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('clicks_count')
                    ->label('Клики')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создана')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('stats')
                    ->label('Статистика')
                    ->icon('heroicon-o-chart-bar')
                    ->url(fn (Link $record) => LinkResource::getUrl('clicks', ['record' => $record])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLinks::route('/'),
            'create' => Pages\CreateLink::route('/create'),
            'clicks' => Pages\LinkClicks::route('/{record}/clicks'),
        ];
    }
}
