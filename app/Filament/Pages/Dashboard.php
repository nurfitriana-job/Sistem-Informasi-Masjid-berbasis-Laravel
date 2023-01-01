<?php

namespace App\Filament\Pages;

use App\Models\Event;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard as BaseDashboard;
use Hugomyb\FilamentMediaAction\Actions\MediaAction;
use Illuminate\Support\HtmlString;

class Dashboard extends BaseDashboard implements HasActions, HasForms
{
    use BaseDashboard\Concerns\HasFiltersForm;
    use InteractsWithActions;
    use InteractsWithForms;

    protected static string $view = 'filament.pages.dashboard';

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        DatePicker::make('startDate')
                            ->default(now()->startOfWeek()->format('Y-m-d'))
                            ->placeholder(fn (Get $get) => now()->startOfWeek()->format('Y-m-d'))
                            ->native(false)
                            ->maxDate(fn (Get $get) => $get('endDate') ?: now()),

                        DatePicker::make('endDate')
                            ->default(now()->endOfWeek())
                            ->placeholder(fn (Get $get) => now()->endOfWeek()->format('Y-m-d'))
                            ->native(false)
                            ->minDate(fn (Get $get) => $get('startDate') ?: now())
                            ->maxDate(now()),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            MediaAction::make('kegiatan')
                ->iconButton()
                ->modalHeading('Kegiatan yang akan datang')
                ->icon('heroicon-o-calendar')
                ->media(function () {
                    $event = Event::where('is_active', true)
                        ->where('start_date', '<=', now())
                        ->where('end_date', '>=', now())
                        ->latest()
                        ->first();
                    if ($event) {
                        return $event->getFirstMediaUrl('events');
                    }

                    return null;
                })
                ->extraAttributes([
                    'x-init' => new HtmlString("
                        if (!localStorage.getItem('lastKegiatanShown') ||
                            new Date() - new Date(localStorage.getItem('lastKegiatanShown')) > 3 * 60 * 60 * 1000) {
                            localStorage.setItem('lastKegiatanShown', new Date());
                            \$wire.mountAction('kegiatan');
                        }
                    "),
                ]),
        ];
    }
}
