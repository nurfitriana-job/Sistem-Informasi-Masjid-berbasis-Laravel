@php
    use App\Settings\GeneralSetting;
    use Illuminate\Support\Facades\Cache;
    use Illuminate\Support\Facades\Schema;

    if (Schema::hasTable('settings')) {
        $settings = Cache::remember('settings', now()->addMinutes(60), function () {
            return app(GeneralSetting::class);
        });
    }

    $latitude = $settings?->site_address_latitude ?? '0.0';
    $longitude = $settings?->site_address_longitude ?? '0.0';
@endphp

<section class="gap our-courses">
    <div class="container">
        <div class="heading">
            <img src="assets/img/heading-img.png" alt="icon">
            <p>Alamat Masjid</p>
        </div>
        <div id="openMap" class="map rounded" style="height: 400px;"></div>
    </div>
</section>
@push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="https://urban96.github.io/L.basemapControl/dist/L.basemapControl.min.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://urban96.github.io/L.basemapControl/dist/L.basemapControl.min.js"></script>
    <script>
        var map = L.map('openMap', {
            center: [{{ $latitude }}, {{ $longitude }}],
            zoom: 15,
            zoomControl: true,
            dragging: true,
            scrollWheelZoom: true,
            touchZoom: true,
            fullscreenControl: true,
            fullscreenControlOptions: {
                position: 'topleft'
            }
        });
        var basemapControl = L.basemapControl({
            position: 'bottomleft',
            layers: [{
                    layer: L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    })
                },
                {
                    layer: L.tileLayer('https://tileserver.memomaps.de/tilegen/{z}/{x}/{y}.png', {
                        maxZoom: 18,
                        attribution: 'Map <a href="https://memomaps.de/">memomaps.de</a> <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    })
                },
                {
                    layer: L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
                        maxZoom: 17,
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    })
                }
            ]
        }).addTo(map);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var customIcon = L.icon({
            iconUrl: `{{ asset('assets/img/marker.png') }}`,
            iconSize: [40, 60],
            iconAnchor: [22, 94],
            popupAnchor: [-3, -76]
        });

        L.marker([{{ $latitude }}, {{ $longitude }}], {
                icon: customIcon
            })
            .addTo(map)
            .bindPopup(
                `<b>{{ $settings?->seo_title ?? config('app.name') }}</b><br>{{ $settings?->site_address ?? '' }}`
            )
            .openPopup();
    </script>
@endpush
