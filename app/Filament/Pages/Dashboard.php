<?php

namespace App\Filament\Pages;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Filament\Forms\Components\Section;
use App\Filament\Widgets\SalesSummary;
use App\Models\User;
use App\Models\Item;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard\Actions\HeaderAction;
use Filament\Pages\Actions\Action;





class Dashboard extends \Filament\Pages\Dashboard
{
    use HasFiltersForm;

    protected static ?string $modelLabel = 'Reports';
    // protected static ?string $pluralModelLabel = 'Reports';

    protected static ?string $navigationLabel = 'Dashboard';
    protected static bool $isLazy = false;

    public function getColumns(): int
    {
        return 3;
    }

    public function filtersForm(Form $form): Form
    {
        return $form->schema([
            //TextInput::make('name'),
            DatePicker::make('startDate'),
            DatePicker::make('endDate'),
            //Toggle::make('active'),
            Select::make('user_id')
                    ->label('Prepared By')
                    ->options(User::where('is_employee', 'Yes')->pluck('name', 'id'))
                    ->placeholder('All')
                    ->reactive(),
             Select::make('live_seller')
                    ->label('Live Seller')
                    ->options(Item::query()
                        ->select('live_seller')
                        ->distinct()
                        ->pluck('live_seller', 'live_seller'))
                    ->placeholder('All')
                    ->reactive(),
            Select::make('mined_from')
                    ->label('Mined From')
                    ->options(Item::query()
                        ->select('mined_from')
                        ->distinct()
                        ->pluck('mined_from', 'mined_from'))
                    ->placeholder('All')
                    ->reactive(),
        ])->columns(3);
    }

     protected function getHeaderActions(): array
    {
        return [
            Action::make('clearFilters')
                ->label('Clear Filters')
                ->color('secondary')
                ->icon('heroicon-o-x-circle') // optional
                ->action(function () {
                    $this->fill([
                        'filters' => [
                            'startDate' => null,
                            'endDate' => null,
                            'user_id' => null,
                            'live_seller' => null,
                        ],
                    ]);
                }),
        ];
    }

    public function getWidgets(): array
    {
        return [
            SalesSummary::class,  
          
        ];
    }
    


}
