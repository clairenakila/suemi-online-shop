<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use App\Models\Item;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class SalesSummary extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?string $pollingInterval = '2s';
    protected static bool $isLazy = false;

    // 🔹 Declare filters for the widget
    protected function getFiltersForm(): Form
    {
        return Form::make()
            ->schema([
                DatePicker::make('startDate')->label('Start Date')->reactive(),
                DatePicker::make('endDate')->label('End Date')->reactive(),
            ]);
    }

    protected function getStats(): array
    {
        $start = $this->filters['startDate'] ?? null;
        $end = $this->filters['endDate'] ?? null;
        $userId = $this->filters['user_id'] ?? null;


        $query = Item::query()
            ->when($start, fn($q) => $q->whereDate('created_at', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('created_at', '<=', $end))
            ->when($userId, fn($q) => $q->where('user_id', $userId));


        // 🔹 Total Quantity respecting the date filter
        $totalQuantity = (clone $query)->sum('quantity');

        $totalCapital = (clone $query)
            ->sum('capital');

        return [
            // Stat::make('Total Items', $query->count())
            //     ->description('Total Items')
            //     ->color('success')
            //     ->chart([1, 2, 3, 7, 3]),

            Stat::make('', $totalQuantity)
                ->description('Total Cleaned Bags')
                ->color('primary')
                ->chart([2, 5, 3, 6, 4]),
            Stat::make('',  '₱' . number_format($totalCapital, 2))
                ->description('Total Capital')
                ->color('primary')
                ->chart([2, 5, 3, 6, 4]),
        ];
    }
}
