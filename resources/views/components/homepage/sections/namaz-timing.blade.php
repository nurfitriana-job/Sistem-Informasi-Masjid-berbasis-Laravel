@php
    $location = request()->get('location');
    $cities = \App\Models\City::where('name', 'like', '%' . $location . '%')->first();
    $prayerTimes = \App\Models\PrayerTime::whereDate('date', date('Y-m-d'))
        ->when($cities, function ($query) use ($cities) {
            return $query->where('city_id', $cities->uid);
        })
        ->first();
    if (!$prayerTimes && $cities) {
        $prayerTimes = \App\Models\PrayerTime::getRows($cities->uid);
        echo '<script>
            window.location.reload();
        </script>';
        exit();
    }
@endphp
<section class="gap no-top">
    <div class="container">
        <div class="namaz-timing">
            <div class="namaz-time">
                <img src="{{ asset('assets/img/namaz-time-icon-1.png') }}" alt="icon">
                <h4>Subuh</h4>
                <h5>
                    {{ $prayerTimes?->subuh->format('H:i A') ?? '05:00 AM' }}
                </h5>
            </div>
            <div class="namaz-time">
                <img src="{{ asset('assets/img/namaz-time-icon-2.png') }}" alt="icon">
                <h4>Zuhr</h4>
                <h5>
                    {{ $prayerTimes?->dzuhur->format('H:i A') ?? '12:30 PM' }}
                </h5>
            </div>
            <div class="namaz-time">
                <img src="{{ asset('assets/img/namaz-time-icon-3.png') }}" alt="icon">
                <h4>Asr</h4>
                <h5>
                    {{ $prayerTimes?->ashar->format('H:i A') ?? '03:30 PM' }}
                </h5>
            </div>
            <div class="namaz-time">
                <img src="{{ asset('assets/img/namaz-time-icon-4.png') }}" alt="icon">
                <h4>Magrib</h4>
                <h5>
                    {{ $prayerTimes?->maghrib->format('H:i A') ?? '06:30 PM' }}
                </h5>
            </div>
            <div class="namaz-time">
                <img src="{{ asset('assets/img/namaz-time-icon-5.png') }}" alt="icon">
                <h4>Isha</h4>
                <h5>
                    {{ $prayerTimes?->isya->format('H:i A') ?? '10:10 PM' }}
                </h5>
            </div>
        </div>
    </div>
</section>
