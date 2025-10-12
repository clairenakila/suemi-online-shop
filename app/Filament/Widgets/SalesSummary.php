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
        $liveSeller = $this->filters['live_seller'] ?? null;



        $query = Item::query()
            ->when($start, fn($q) => $q->whereDate('created_at', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('created_at', '<=', $end))
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->when($liveSeller, fn($q) => $q->where('live_seller', $liveSeller));


        // Returned items query (uses date_returned)
        $returnedItemsQuery = (clone $query)
            ->where('is_returned', 'Yes')
            ->when($start, fn($q) => $q->whereDate('date_returned', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('date_returned', '<=', $end));

        // 🔹 Total Quantity respecting the date filter
        $countCleanedItems = (clone $query)->sum('quantity');

        $totalCapital = (clone $query)
            ->sum('capital');

        $beforeShoppeeCommission = (clone $query)
            ->sum('selling_price');

        $shoppeeCommission = (clone $query)
            ->sum('shoppee_commission');
        
        $afterShoppeeCommission = (clone $query)->sum('total_gross_sale');

        $rtsCount = (clone $query)
            ->where('is_returned', 'Yes')
            ->sum('quantity');

        $rtsAmount = (clone $query)
            ->where('is_returned', 'Yes')
            ->sum('selling_price');
        
        $totalSale = $afterShoppeeCommission - $rtsAmount;


        return [
            // Stat::make('Total Items', $query->count())
            //     ->description('Total Items')
            //     ->color('success')
            //     ->chart([1, 2, 3, 7, 3]),

            Stat::make('', $countCleanedItems . ' pcs.')
                ->description('Count of Cleaned Items')
                ->color('primary')
                ->chart([2, 5, 3, 6, 4]),
            Stat::make('',  '₱' . number_format($totalCapital, 2))
                ->description('Total Capital')
                ->color('primary')
                ->chart([2, 5, 3, 6, 4]),
             Stat::make('',  '₱' . number_format($beforeShoppeeCommission, 2))
                ->description('Total Sales Before Shoppee Commission')
                ->color('primary')
                ->chart([2, 5, 3, 6, 4]),
            Stat::make('',  '₱' . number_format($shoppeeCommission, 2))
                ->description('Shoppee Commission')
                ->color('primary')
                ->chart([2, 5, 3, 6, 4]),
            Stat::make('',  '₱' . number_format($afterShoppeeCommission, 2))
                ->description('Total Sales After Shoppee Commission')
                ->color('primary')
                ->chart([2, 5, 3, 6, 4]),
            Stat::make('',$rtsCount .' pcs.')
                ->description('Count of Returned Items')
                ->color('danger')
                ->chart([2, 5, 3, 6, 4]),
            Stat::make('',  '₱' . number_format($rtsAmount))
                ->description('Total Amount for Returned Items')
                ->color('danger')
                ->chart([2, 5, 3, 6, 4]),
            Stat::make('',  '₱' . number_format($totalSale))
                ->description('Total Sales (Deducted na sa Total Amount for Returned Items)')
                ->color('success')
                ->chart([2, 5, 3, 6, 4]),
            
        ];
    }
}
