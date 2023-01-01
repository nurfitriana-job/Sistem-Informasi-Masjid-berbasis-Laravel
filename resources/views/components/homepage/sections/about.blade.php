@php
    use Illuminate\Support\Facades\Cache;
    use App\Settings\AboutSetting;
    use Illuminate\Support\Str;

    $about = Cache::remember('about_settings', now()->addMinutes(60), function () {
        return app(AboutSetting::class);
    });
@endphp
<section class="position-relative mb-5" id="about">
    <div class="container">
        <div class="heading">
            <img src="{{ asset('assets/img/heading-img.png') }}" alt="icon">
            <p>
                {{ $about?->title ?? 'About Us' }}
            </p>
            <h2>
                {{ $about?->subtitle ?? '' }}
            </h2>
        </div>
        <div class="row">
            <div class="col-lg-5">
                <div class="islamic-history">
                    {!! Str::markdown($about?->description ?? '') !!}

                    @if ($about?->show_button)
                        <br />
                        <a href="{{ $about?->button_link ?? '#' }}"
                            class="btn">{{ $about?->button_text ?? 'Read More' }}</a>
                    @endif
                </div>
            </div>
            <div class="col-lg-7">
                <div class="d-none d-lg-block col-6 position-relative" style="width: 100%;">
                    <img src="{{ asset($about?->image ? '/storage/' . $about?->image : 'assets/img/about-img.png') }}"
                        alt="icon" style="max-width: 100%;">
                    <img src="{{ asset('assets/images/image_cover.svg') }}" class="position-absolute top-0 start-0">
                </div>
            </div>
        </div>
</section>
