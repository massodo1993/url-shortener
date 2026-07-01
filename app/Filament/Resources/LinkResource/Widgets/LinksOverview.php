<?php

namespace App\Filament\Resources\LinkResource\Widgets;

use App\Models\Click;
use App\Models\Link;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LinksOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $userId = auth()->id();

        $linksCount = Link::where('user_id', $userId)->count();

        $totalClicks = Click::whereHas('link', fn ($q) => $q->where('user_id', $userId))->count();

        return [
            Stat::make('Всего ссылок', $linksCount)
                ->icon('heroicon-o-link'),
            Stat::make('Всего переходов', $totalClicks)
                ->icon('heroicon-o-cursor-arrow-rays')
                ->color('success'),
        ];
    }
}
