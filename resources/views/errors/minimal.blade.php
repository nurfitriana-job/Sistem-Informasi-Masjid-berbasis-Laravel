@php
    use App\Settings\GeneralSetting;
    use Illuminate\Support\Facades\Cache;
    use Illuminate\Support\Facades\Schema;
    use RalphJSmit\Laravel\SEO\Support\SEOData;
    use RalphJSmit\Laravel\SEO\SchemaCollection;

    $settings = null;
    $seoData = null;

    $primaryColor = '#fbc50b';
    $secondaryColor = '#007d3a';

    if (Schema::hasTable('settings')) {
        $settings = Cache::remember('settings', now()->addMinutes(60), function () {
            return app(GeneralSetting::class);
        });

        $primaryColor = $settings->theme_color ?? '#fbc50b';
        $secondaryColor = $settings->secondary_color ?? '#007d3a';

        $seoData = new SEOData(
            title: $settings->seo_title ?? config('app.name'),
            description: $settings->seo_description ?? null,
            image: $settings->site_logo ?? 'assets/images/logo.png',
            favicon: $settings->site_favicon ?? 'assets/images/logo.png',
            site_name: $settings->site_name ?? config('app.name'),
            section: 'website',
            author: $settings->site_name ?? config('app.name'),
            locale: app()->getLocale(),
            schema: SchemaCollection::make()->add(
                fn(SEOData $SEOData) => [
                    '@context' => 'https://schema.org',
                    '@type' => 'WebPage',
                    'name' => $settings->site_name ?? config('app.name'),
                    'url' => url('/'),
                    'description' => $settings->seo_description ?? 'Welcome to our mosque website',
                    'mainEntityOfPage' => [
                        '@type' => 'WebSite',
                        'name' => $settings->site_name ?? config('app.name'),
                        'url' => url('/'),
                    ],
                    'publisher' => [
                        '@type' => 'Organization',
                        'name' => $settings->site_name ?? config('app.name'),
                        'logo' => [
                            '@type' => 'ImageObject',
                            'url' => $settings->site_logo ?? 'assets/images/logo.png',
                        ],
                        'mainEntity' => [
                            '@type' => 'Place',
                            'name' => $settings->site_name ?? config('app.name'),
                            'address' => [
                                '@type' => 'PostalAddress',
                                'streetAddress' => $settings->site_address ?? 'Address not set',
                                'addressCountry' => 'Indonesia',
                            ],
                            'geo' => [
                                '@type' => 'GeoCoordinates',
                                'latitude' => $settings->site_address_latitude ?? '0.0',
                                'longitude' => $settings->site_address_longitude ?? '0.0',
                            ],
                            'sameAs' => array_values($settings->site_social_links) ?? [
                                'https://www.facebook.com/mosque',
                            ],
                        ],
                    ],
                ],
            ),
        );
    }

    $colors = [
        'primary' => $primaryColor,
        'secondary' => $secondaryColor,
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />

    <meta name="application-name" content="{{ config('app.name') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    {!! seo($seoData) !!}

    <style>
        :root {
            --theme-colour: {{ $primaryColor ?? '#fbc50b' }} !important;
            --common-colour: {{ $secondaryColor ?? '#007d3a' }} !important;
            --common-font: 'Sawarabi Mincho';
        }
    </style>
    <style>
        [x-cloak] {
            display: none !important;
        }

        .float {
            position: fixed;
            width: 60px;
            height: 60px;
            bottom: 40px;
            right: 40px;
            background-color: #25d366;
            color: #FFF;
            border-radius: 50px;
            text-align: center;
            font-size: 30px;
            box-shadow: 2px 2px 3px #999;
            z-index: 100;
        }

        .my-float {
            margin-top: 16px;
        }
    </style>
    <!-- CSS only -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <!-- fancybox -->
    <link rel="stylesheet" href="{{ asset('assets/css/jquery.fancybox.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/nice-select.css') }}">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="{{ asset('assets/css/fontawesome.min.css') }}">
    <!-- style -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <!-- responsive -->
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}">
    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/preloader.js') }}"></script>

    @stack('styles')
</head>

<body class="antialiased">
    <x-homepage.header :settings="$settings" :colors="$colors" />
    @php
        use Illuminate\Support\Str;

        $slide = \App\Models\Slider::where('is_active', 1)->first();
    @endphp
    <section class="error-404"
        style="background-image: url({{ $slide->getFirstMediaUrl('background_image') ?? asset('assets/img/bannr-img.jpg') }});">
        <div class="container">
            <div class="row">
                <div class="error">
                    <h2>
                        @yield('code', '404')
                    </h2>
                    <h3>@yield('message')</h3>
                    <p>
                        @yield('description', 'Sorry, the page you are looking for could not be found.')
                    </p>
                    <a href="/" class="btn mt-3"><i class="fa-solid fa-house"></i><span>Back To Home</span></a>
                </div>
            </div>
        </div>
    </section>
    <x-homepage.footer :settings="$settings" :colors="$colors" />

    <div id="progress">
        <span id="progress-value"><i class="fa-solid fa-up-long"></i></span>
    </div>

    <a href="https://api.whatsapp.com/send?phone={{ $settings->site_support_phone ?? '' }}&text=
        {{ urlencode('Assalamualaikum, saya ingin bertanya tentang...') }}"
        class="float" target="_blank">
        <i class="fa-brands fa-whatsapp my-float"></i>
    </a>
    @livewire('notifications')
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('assets/js/circle-progres.js') }}"></script>
    <!-- fancybox -->
    <script src="{{ asset('assets/js/jquery.fancybox.min.js') }}"></script>
    <script src="{{ asset('assets/js/audioplayer.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    @filamentScripts
    @stack('scripts')

</body>

</html>
