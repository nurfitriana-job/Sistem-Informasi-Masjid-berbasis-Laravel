@php
    use Illuminate\Support\Str;

    $slides = \App\Models\Slider::where('is_active', 1)->get();
@endphp
<section class="slider-home-1 owl-carousel owl-theme" wire:ignore>
    @foreach ($slides as $slide)
        <div class="hero-section item" id="{{ $loop->index }}" style="min-height: 100vh !important;">
            <img src="{{ $slide->getFirstMediaUrl('background_image') }}" alt="hero-img" class="hero-img-style">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="hero-text">
                            <h1 title="{{ $slide->title }}">
                                {{ Str::limit($slide->title, 50, '...') }}</h1>
                            <p title="{{ $slide->description }}">{{ Str::limit($slide->description, 100, '...') }}</p>
                            @if (!empty($slide->link))
                                <a href="{{ $slide->link }}" class="btn">Discover</a>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="hero-img">
                            @if (!empty($slide->getFirstMediaUrl('hero_image')))
                                <img src="{{ $slide->getFirstMediaUrl('hero_image') }}" alt="hero-img">
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</section>
