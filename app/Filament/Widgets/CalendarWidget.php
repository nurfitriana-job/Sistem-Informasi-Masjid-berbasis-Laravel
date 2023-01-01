<?php

namespace App\Filament\Widgets;

use App\Forms\EventForm;
use App\Models\Event;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 2;

    protected static ?string $pollingInterval = null;

    public Model | string | null $model = Event::class;

    protected function headerActions(): array
    {
        return [
            //
        ];
    }

    protected function modalActions(): array
    {
        return [
            //
        ];
    }

    public function getFormSchema(): array
    {
        return EventForm::make();
    }

    public function fetchEvents(array $fetchInfo): array
    {
        $event = Event::query()
            ->get()
            ->map(
                fn (Event $event) => [
                    'id' => $event->id,
                    'type' => $event->category->name,
                    'title' => $event->name,
                    'start' => $event->start_date,
                    'end' => $event->end_date,
                    'description' => $event->description,
                ]
            )
            ->toArray();

        return $event;
    }
}
