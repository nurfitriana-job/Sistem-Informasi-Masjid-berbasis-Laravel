<?php

namespace App\Providers\Filament;

use Filament\Panel;
use App\Models\User;
use Spatie\Color\Hex;
use Filament\PanelProvider;
use App\Filament\Pages\Login;
use Filament\Enums\ThemeMode;
use App\Filament\Pages\Backups;
use App\Filament\Pages\Register;
use App\Settings\GeneralSetting;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\MyProfile;
use Filament\View\PanelsRenderHook;
use Filament\Support\Enums\Platform;
use Illuminate\Support\Facades\Blade;
use App\Livewire\Profile\PersonalInfo;
use App\Filament\Widgets\CalendarWidget;
use Filament\Forms\Components\FileUpload;
use App\Filament\Widgets\TransactionStats;
use Filament\Http\Middleware\Authenticate;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use Rmsramos\Activitylog\ActivitylogPlugin;
use pxlrbt\FilamentSpotlight\SpotlightPlugin;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Awcodes\FilamentQuickCreate\QuickCreatePlugin;
use DutchCodingCompany\FilamentSocialite\Provider;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use BezhanSalleh\FilamentExceptions\FilamentExceptionsPlugin;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use ShuvroRoy\FilamentSpatieLaravelBackup\FilamentSpatieLaravelBackupPlugin;

class AdminPanelProvider extends PanelProvider
{
    private ?GeneralSetting $settings = null;

    public function __construct()
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $this->settings = app(GeneralSetting::class);
            }
        } catch (\Exception $e) {
            $this->settings = null;
        }
    }

    public function panel(Panel $panel): Panel
    {
        $theme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light';
        $logo = asset($this->settings?->site_logo ? 'storage/' . $this->settings?->site_logo : 'assets/images/logo.png');
        if ($theme === 'dark') {
            $logo = asset($this->settings?->site_logo_dark ? 'storage/' . $this->settings?->site_logo_dark : 'assets/images/logo.png');
        }

        $panel->renderHook(
            PanelsRenderHook::BODY_START,
            fn(): string => Blade::render('@livewire(\'announcement\')'),
        );

        return $panel
            ->default()
            ->id('admin')
            ->path('panel')
            ->when($this->settings?->login_enabled ?? true, fn($panel) => $panel->login(Login::class))
            ->when($this->settings?->registration_enabled ?? true, fn($panel) => $panel->registration(Register::class))
            ->when($this->settings?->password_reset_enabled ?? true, fn($panel) => $panel->passwordReset())
            ->when($this->settings?->email_verification_enabled ?? true, fn($panel) => $panel->emailVerification())
            ->colors([
                'primary' => Hex::fromString($this->settings?->theme_color ?? '#3c5b99'),
                'secondary' => Hex::fromString($this->settings?->secondary_color ?? '#3c5b99'),
            ])
            ->brandLogo(fn() => $logo)
            ->favicon(fn() => asset($this->settings?->site_favicon ? 'storage/' . $this->settings?->site_favicon : 'assets/images/logo.png'))
            ->brandName($this->settings?->site_name ?? config('app.name'))
            ->brandLogoHeight($this->settings?->logo_height ?? '5rem')
            ->sidebarWidth($this->settings?->sidebar_width ?? '16rem')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->pages([
                Dashboard::class,
            ])
            ->widgets([
                TransactionStats::class,
                CalendarWidget::class,
            ])
            ->navigationGroups(['Acara', 'Inventaris', 'Transaksi', 'Manajemen Keuangan', 'Manajemen Akun', 'Pengaturan'])
            ->databaseTransactions()
            ->broadcasting(true)
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->sidebarCollapsibleOnDesktop(true)
            ->defaultThemeMode(ThemeMode::Light)
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins(
                $this->getPlugins()
            )
            ->databaseNotifications()
            ->globalSearchFieldSuffix(fn(): ?string => match (Platform::detect()) {
                Platform::Windows, Platform::Linux => 'CTRL+K',
                Platform::Mac => '⌘K',
                default => null,
            });
    }

    private function getPlugins(): array
    {
        $plugins = [
            FilamentShieldPlugin::make(),
            BreezyCore::make()
                ->customMyProfilePage(
                    MyProfile::class,
                )
                ->myProfile(
                    shouldRegisterUserMenu: true,
                    shouldRegisterNavigation: false,
                    navigationGroup: 'Settings',
                    hasAvatars: true,
                    slug: 'my-profile',
                )
                ->myProfileComponents([
                    'personal_info' => PersonalInfo::class,
                ])
                ->enableBrowserSessions(condition: true)
                ->avatarUploadComponent(fn($fileUpload) => $fileUpload->disableLabel())
                ->avatarUploadComponent(
                    fn() => FileUpload::make('avatar_url')
                        ->label(__('Avatar'))
                        ->image()
                        ->columnSpanFull()
                        ->avatar()
                        ->alignment('center')
                        ->disk('public')
                )
                ->enableTwoFactorAuthentication(),
            FilamentSpatieLaravelBackupPlugin::make()
                ->usingPage(Backups::class),
            QuickCreatePlugin::make()
                ->excludes([
                    \App\Filament\Resources\UserResource::class,
                ]),
            ActivitylogPlugin::make(),
            FilamentExceptionsPlugin::make(),
            FilamentApexChartsPlugin::make(),
            FilamentFullCalendarPlugin::make()
                ->selectable()
                ->editable()
                ->timezone(config('app.timezone'))
                ->locale(config('app.locale')),
            SpotlightPlugin::make(),
        ];

        if ($this->settings->sso_enabled ?? true) {
            $plugins[] =
                FilamentSocialitePlugin::make()
                ->providers([
                    Provider::make('google')
                        ->label('Google')
                        ->icon('fab-google')
                        ->outlined(true)
                        ->stateless(false),
                ])->registration(true)
                ->createUserUsing(function (string $provider, SocialiteUserContract $oauthUser, FilamentSocialitePlugin $plugin) {
                    $user = User::firstOrNew([
                        'email' => $oauthUser->getEmail(),
                    ]);
                    $user->name = $oauthUser->getName();
                    $user->username = $oauthUser->getNickname();
                    $user->email = $oauthUser->getEmail();
                    $user->email_verified_at = now();
                    $user->avatar_url = $oauthUser->getAvatar();
                    $user->save();

                    $user->assignRole('user');

                    return $user;
                });
        }

        return $plugins;
    }
}
