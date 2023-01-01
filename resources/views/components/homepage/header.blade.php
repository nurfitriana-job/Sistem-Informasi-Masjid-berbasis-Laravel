@props(['settings', 'colors'])
<header>
    <div class="container">
        <div class="top-bar">
            <div class="row align-items-center">
                <div class="col-xl-5">
                    <div class="d-flex align-items-center">
                        <div class="content-header me-5">
                            <i>
                                <svg width="800px" height="800px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="12" r="5" stroke="{{ $colors['primary'] }}"
                                        stroke-width="1.5" />
                                    <path d="M12 2V4" stroke="{{ $colors['primary'] }}" stroke-width="1.5"
                                        stroke-linecap="round" />
                                    <path d="M12 20V22" stroke="{{ $colors['primary'] }}" stroke-width="1.5"
                                        stroke-linecap="round" />
                                    <path d="M4 12L2 12" stroke="{{ $colors['primary'] }}" stroke-width="1.5"
                                        stroke-linecap="round" />
                                    <path d="M22 12L20 12" stroke="{{ $colors['primary'] }}" stroke-width="1.5"
                                        stroke-linecap="round" />
                                    <path d="M19.7778 4.22266L17.5558 6.25424" stroke="{{ $colors['primary'] }}"
                                        stroke-width="1.5" stroke-linecap="round" />
                                    <path d="M4.22217 4.22266L6.44418 6.25424" stroke="{{ $colors['primary'] }}"
                                        stroke-width="1.5" stroke-linecap="round" />
                                    <path d="M6.44434 17.5557L4.22211 19.7779" stroke="{{ $colors['primary'] }}"
                                        stroke-width="1.5" stroke-linecap="round" />
                                    <path d="M19.7778 19.7773L17.5558 17.5551" stroke="{{ $colors['primary'] }}"
                                        stroke-width="1.5" stroke-linecap="round" />
                                </svg>
                            </i>
                            <h4>Jam Operasional:
                                <b id="sunrise-time">
                                    @php
                                        $operationalHours = $settings->operating_hours;
                                        $today = strtolower(date('l'));
                                        $openTime = $operationalHours[$today];
                                    @endphp
                                    {{ $openTime }}
                                </b>
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2">
                    <div class="">
                        <ul class="social-media">
                            @php
                                $socialLinks = $settings->site_social_links;
                            @endphp
                            @foreach ($socialLinks as $key => $item)
                                @php
                                    if (empty($item)) {
                                        continue;
                                    }
                                    $icon = '';
                                    switch ($key) {
                                        case 'tiktok':
                                            $icon = 'fa-brands fa-tiktok';
                                            break;
                                        case 'twitter':
                                            $icon = 'fab fa-twitter';
                                            break;
                                        case 'youtube':
                                            $icon = 'fa-brands fa-youtube';
                                            break;
                                        case 'facebook':
                                            $icon = 'fab fa-facebook-f';
                                            break;
                                        case 'instagram':
                                            $icon = 'fab fa-instagram';
                                            break;
                                    }
                                @endphp
                                <li><a href="{{ $item }}" target="_blank" rel="noopener noreferrer"><i
                                            class="{{ $icon }}"></i></a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-xl-5">
                    <div class="d-flex align-items-center login">
                        <div class="location d-flex align-items-center">
                            <i>
                                <svg width="24px" height="24px" viewBox="-4 0 32 32" version="1.1"
                                    xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <g transform="translate(-104.000000, -411.000000)"
                                            fill="{{ $colors['primary'] }}">
                                            <path
                                                d="M116,426 C114.343,426 113,424.657 113,423 C113,421.343 114.343,420 116,420 C117.657,420 119,421.343 119,423 C119,424.657 117.657,426 116,426 L116,426 Z M116,418 C113.239,418 111,420.238 111,423 C111,425.762 113.239,428 116,428 C118.761,428 121,425.762 121,423 C121,420.238 118.761,418 116,418 L116,418 Z M116,440 C114.337,440.009 106,427.181 106,423 C106,417.478 110.477,413 116,413 C121.523,413 126,417.478 126,423 C126,427.125 117.637,440.009 116,440 L116,440 Z M116,411 C109.373,411 104,416.373 104,423 C104,428.018 114.005,443.011 116,443 C117.964,443.011 128,427.95 128,423 C128,416.373 122.627,411 116,411 L116,411 Z">
                                            </path>
                                        </g>
                                    </g>
                                </svg>
                            </i>
                            <span id="location">Fetching location...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bottom-bar">
            <div class="two-bar">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="logo">
                        <a href="/" class="d-flex align-items-center w-50 gap-2">
                            <img alt="logo"
                                src="{{ asset($settings->site_logo ? '/storage/' . $settings->site_logo : 'assets/images/logo.png') }}"
                                class="w-25">
                            <h5 class="text-white">{{ $settings->seo_title ?? config('app.name') }}</h5>
                        </a>
                    </div>
                    <div class="bar-menu">
                        <i class="fa-solid fa-bars"></i>
                    </div>
                </div>

                <div class="header-search">
                    <nav class="navbar">
                        <ul class="navbar-links">
                            <li class="navbar-dropdown">
                                <a href="/">Home</a>
                            </li>
                            <li class="navbar-dropdown">
                                <a href="#about">About</a>
                            </li>
                            <li class="navbar-dropdown">
                                <a href="#contact">Contact</a>
                            </li>
                        </ul>
                    </nav>
                    <div class="header-search-button search-box-outer pt-2">
                        <nav class="navbar">
                            <ul class="navbar-links">
                                <li class="navbar-dropdown">
                                    <a href="{{ route('filament.admin.auth.login') }}">
                                        @auth
                                            {{ auth()->user()->name }}
                                        @else
                                            <i class="fa-solid fa-user mx-1"></i>
                                            <span>Login</span>
                                        @endauth
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mobile-nav hmburger-menu" id="mobile-nav"
        style="display:block; background:{{ $colors['secondary'] }}; ">
        <div class="res-log">
            <a href="/">
                <img src="{{ asset($settings->site_logo ? '/storage/' . $settings->site_logo : 'assets/images/logo.png') }}"
                    alt="Responsive Logo" class="white-logo w-25">
            </a>
        </div>
        <ul>
            <li>
                <a href="/" style="color: {{ $colors['primary'] }}">Home</a>
            </li>
            <li><a href="#about" style="color: {{ $colors['primary'] }}">about</a></li>
            <li><a href="#contact" style="color: {{ $colors['primary'] }}">Contact</a></li>
            <li>
                <a style="color: {{ $colors['primary'] }}" href="{{ route('filament.admin.auth.login') }}">
                    @auth
                        {{ auth()->user()->name }}
                    @else
                        <span>Login</span>
                    @endauth
                </a>
            </li>
            @guest
                <li>
                    @if (Route::has('filament.admin.auth.register'))
                        <a style="color: {{ $colors['primary'] }}" href="{{ route('filament.admin.auth.register') }}">
                            <span>Register</span>
                        </a>
                    @endif
                </li>
            @endguest
        </ul>
        <a href="JavaScript:void(0)" style="color: {{ $colors['primary'] }}" id="res-cross">
        </a>
    </div>
</header>
@push('scripts')
    <script>
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var latitude = position.coords.latitude;
                var longitude = position.coords.longitude;

                var apiUrl =
                    `https://api.opencagedata.com/geocode/v1/json?q=${latitude}+${longitude}&key={{ config('services.opencagedata.api_key') }}`;

                fetch(apiUrl)
                    .then(response => response.json())
                    .then(data => {
                        if (data.results && data.results[0]) {
                            var location = data.results[0].components.city || data.results[0].components
                                .country;

                            document.getElementById('location').textContent = location;

                            var url = new URL(window.location.href);
                            url.searchParams.set('location', location);
                            window.history.pushState({}, '', url);
                        } else {
                            console.log(data)
                            document.getElementById('location').textContent = 'Location not found';
                        }
                    })
                    .catch(error => {
                        console.error("Error fetching location: ", error);
                        document.getElementById('location').textContent = 'Error fetching location';
                    });
            });
        } else {
            document.getElementById('location').textContent = 'Geolocation is not supported by this browser.';
        }
    </script>
@endpush
