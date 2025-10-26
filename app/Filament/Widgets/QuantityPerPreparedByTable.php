<?php

namespace App\Filament\Widgets;

use App\Models\Item;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class QuantityPerPreparedByTable extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Quantity per Prepared By';
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getQuery())
            ->columns([
                Tables\Columns\TextColumn::make('prepared_by')
                    ->label('Prepared By')
                    ->badge()
                    ->color(fn($record) => match (true) {
                        $record->total_quantity >= 1000 => 'success',
                        $record->total_quantity >= 500 => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_quantity')
                    ->label('Total Quantity')
                    ->numeric()
                    ->alignEnd()
                    ->color(fn($record) => match (true) {
                        $record->total_quantity >= 1000 => 'success',
                        $record->total_quantity >= 500 => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->paginated(false)
            ->striped();
    }

    protected function getQuery(): Builder
    {
        $start = $this->filters['startDate'] ?? null;
        $end = $this->filters['endDate'] ?? null;
        $minedFrom = $this->filters['mined_from'] ?? null;

        return Item::query()
            ->select([
                'items.user_id as id', // ✅ alias ensures Filament always has a key
                DB::raw('COALESCE((SELECT users.name FROM users WHERE users.id = items.user_id LIMIT 1), "Unknown") as prepared_by'),
                DB::raw('SUM(items.quantity) as total_quantity'),
            ])
            ->when($start, fn($q) => $q->whereDate('items.created_at', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('items.created_at', '<=', $end))
            ->when($minedFrom, fn($q) => $q->where('items.mined_from', $minedFrom))
            ->groupBy('items.user_id');
    }
}
