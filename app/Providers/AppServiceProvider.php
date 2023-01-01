<?php

namespace App\Providers;

use App\Policies\ActivityPolicy;
use App\Policies\ExceptionPolicy;
use BezhanSalleh\FilamentExceptions\Models\Exception;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\Alignment;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Columns\TextColumn;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\Models\Activity;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        parent::register();

        // Register render hook for app.js
        FilamentView::registerRenderHook('panels::body.end', fn (): string => Blade::render("@vite('resources/js/app.js')"));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Activity::class, ActivityPolicy::class);
        Gate::policy(Exception::class, ExceptionPolicy::class);

        // Number::setLocale(config('app.locale'));

        // Register Filament icons
        $this->registerFilamentIcons();

        // Register render hooks for authentication login form
        $this->registerAuthRenderHook();

        // Configure action alignment for modals
        $this->configureActionAlignments();

        // Configure table and form components
        $this->configureTableAndFormComponents();

        $url = parse_url(config('app.url'));
        if ($url['scheme'] == 'https') {
            URL::forceScheme('https');
        }

        Notification::configureUsing(function (Notification $notification): void {
            $notification->view('components.notification');
        });
    }

    /**
     * Register Filament icons.
     */
    private function registerFilamentIcons(): void
    {
        FilamentIcon::register([
            'panels::sidebar.collapse-button' => 'heroicon-o-bars-3-bottom-right',
            'panels::sidebar.expand-button' => 'heroicon-o-bars-3',
        ]);
    }

    /**
     * Register render hook for authentication login form.
     */
    private function registerAuthRenderHook(): void
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
            fn (): View => view('components.auth.login.footer')
        );
    }

    /**
     * Configure action alignments for modals and table actions.
     */
    private function configureActionAlignments(): void
    {
        Action::configureUsing(fn (Action $action) => $action->modalFooterActionsAlignment(Alignment::End));

        TableAction::configureUsing(fn (TableAction $action) => $action->modalFooterActionsAlignment(Alignment::End));

        // Set form actions alignment to the right
        Page::formActionsAlignment(Alignment::Right);
    }

    /**
     * Configure table and form components.
     */
    private function configureTableAndFormComponents(): void
    {
        // Configure default text column behavior
        TextColumn::configureUsing(fn (TextColumn $component) => $component->default('-'));
    }
}
