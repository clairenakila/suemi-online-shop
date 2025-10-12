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





class Dashboard extends \Filament\Pages\Dashboard
{
    use HasFiltersForm;

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
                    ->label('User')
                    ->options(User::pluck('name', 'id'))
                    ->placeholder('All Users')
                    ->reactive(),
        ])->columns(3);
    }
    public function getWidgets(): array
    {
        return [
            SalesSummary::class,  
          
        ];
    }


}
