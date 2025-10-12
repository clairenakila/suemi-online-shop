<?php

namespace App\Filament\Resources\ItemResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Item;

class ItemSalesSummary extends BaseWidget implements HasForms
{
    use InteractsWithForms;

    public ?string $filterDate = null;

    protected static ?string $pollingInterval = '2s'; // optional auto-refresh
    protected static bool $isLazy = false;

    protected function getFormSchema(): array
    {
        return [
            DatePicker::make('filterDate')
                ->label('Filter by Date')
                ->reactive(),
        ];
    }

    protected function getStats(): array
    {
        $query = Item::query();

        if ($this->filterDate) {
            $query->whereDate('created_at', $this->filterDate);
        }
        
        $totalCapital = (clone $query)
            ->sum('capital');

        $shoppeeCommission = (clone $query)
            ->sum('shoppee_commission');

        $noShoppeeCommission = (clone $query)
            ->sum('selling_price');

        $totalGrossSale = (clone $query)->sum('total_gross_sale');

        $returnedTotalSellingPrice = (clone $query)
            ->where('is_returned', 'Yes')
            ->sum('selling_price');

        $netSales = $totalGrossSale - $returnedTotalSellingPrice;

        return [
            Stat::make('Total Capital', '₱' . number_format($totalCapital, 2))
                ->color('success'),
            Stat::make('Sales Before Shoppee Commission', '₱' . number_format($noShoppeeCommission, 2))
                ->color('success'),
            Stat::make('Shoppee Commission', '₱' . number_format($shoppeeCommission, 2))
                ->color('success'),
            Stat::make('Sales After Shoppee Deduction', '₱' . number_format($totalGrossSale, 2))
                ->color('success'),

            Stat::make('Total Amount for Returned Items', '₱' . number_format($returnedTotalSellingPrice, 2))
                ->color('danger'),

          Stat::make('Total Sales (Deducted na sa Total Amount for Returned Items)', '₱' . number_format($netSales, 2))
->color('primary'),    


        ];
    }
}
