@php
    $brandName = filament()->getBrandName();
    $brandLogo = filament()->getBrandLogo();
    $brandLogoHeight = filament()->getBrandLogoHeight() ?? '1.5rem';
    $darkModeBrandLogo = filament()->getDarkModeBrandLogo();
    $hasDarkModeBrandLogo = filled($darkModeBrandLogo);

    $getLogoClasses = fn(bool $isDarkMode): string => \Illuminate\Support\Arr::toCssClasses([
        'fi-logo',
        'flex' => !$hasDarkModeBrandLogo,
        'flex dark:hidden' => $hasDarkModeBrandLogo && !$isDarkMode,
        'hidden dark:flex' => $hasDarkModeBrandLogo && $isDarkMode,
    ]);

    $logoStyles = "height: {$brandLogoHeight}";
@endphp

@capture($content, $logo, $isDarkMode = false)
    @guest
        @if ($logo instanceof \Illuminate\Contracts\Support\Htmlable)
            <div {{ $attributes->class([$getLogoClasses($isDarkMode)])->style([$logoStyles]) }}>
                {{ $logo }}
            </div>
        @elseif (filled($logo))
            <img alt="{{ __('filament-panels::layout.logo.alt', ['name' => $brandName]) }}" src="{{ $logo }}"
                {{ $attributes->class([$getLogoClasses($isDarkMode)])->style([$logoStyles]) }} />
        @else
            <div
                {{ $attributes->class([
                    $getLogoClasses($isDarkMode),
                    'text-xl font-bold leading-5 tracking-tight text-gray-950 dark:text-white',
                ]) }}>
                {{ $brandName }}
            </div>
        @endif
    @endguest
    @auth
        <div class="flex items-center gap-x-2 ">
            <img alt="{{ __('filament-panels::layout.logo.alt', ['name' => $brandName]) }}" src="{{ $logo }}"
                {{ $attributes->class([$getLogoClasses($isDarkMode)])->style(['height: 3rem']) }} />
            <div
                {{ $attributes->class([$getLogoClasses($isDarkMode), 'text-lg font-bold leading-5 tracking-tight text-gray-600']) }}>
                {{ $brandName }}
            </div>
        </div>
    @endauth
@endcapture

{{ $content($brandLogo) }}

@if ($hasDarkModeBrandLogo)
    {{ $content($darkModeBrandLogo, isDarkMode: true) }}
@endif
