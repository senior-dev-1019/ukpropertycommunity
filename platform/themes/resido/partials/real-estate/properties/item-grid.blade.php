@php
    $img_slider = isset($img_slider) ? $img_slider : true;
    $is_lazyload = isset($lazyload) ? $lazyload : true;
@endphp

<div class="property-listing property-2 {{ $class_extend ?? '' }}"
     data-lat="{{ $property->latitude }}"
     data-long="{{ $property->longitude }}">
    <div class="listing-img-wrapper">
        <div class="list-img-slide">
            <div class="click @if(!$img_slider) not-slider @endif">
                @foreach ($property['images'] as $image)
                    <div>
                        <a href="{{ $property->url }}">
                            @if($is_lazyload)
                            <img src="{{ get_image_loading() }}"
                                data-src="{{ RvMedia::getImageUrl($image, 'medium', false, RvMedia::getDefaultImage()) }}"
                                class="img-fluid mx-auto lazy" alt="{{ $property->name }}"/>
                            @else
                            <img src="{{ RvMedia::getImageUrl($image, 'medium', false, RvMedia::getDefaultImage()) }}"
                                class="img-fluid mx-auto" alt="{{ $property->name }}"/>
                            @endif
                        </a>
                    </div>
                    @if(!$img_slider) @break @endif
                @endforeach
            </div>
        </div>
    </div>

    <div class="listing-detail-wrapper">
        <div class="listing-short-detail-wrap">
            <div class="listing-short-detail">
                <div class="list-price d-flex justify-content-between">
                    <span class="prt-types {{ $property->type->slug }}">{{ $property->type_name }}</span>
                    <h6 class="listing-card-info-price">
                        {{ $property->price_html }}
                    </h6>
                </div>
                <h4 class="listing-name">
                    <a href="{{ $property->url }}" class="prt-link-detail"
                       title="{{ $property->name }}">{!! clean($property->name) !!}</a>
                </h4>
                @if (is_review_enabled() && $property->reviews_count > 0)
                    {!! Theme::partial('real-estate.elements.property-review', compact('property')) !!}
                @endif
            </div>
        </div>
    </div>

    <div class="price-features-wrapper">
        <div class="list-fx-features">
            <div class="listing-card-info-icon">
                <div class="inc-fleat-icon">
                    <img src="{{ Theme::asset()->url('img/bed.svg') }}" width="13" alt=""/>
                </div>
                {!! clean($property->number_bedroom) !!} {!! __('Beds') !!}
            </div>
            <div class="listing-card-info-icon">
                <div class="inc-fleat-icon">
                    <img src="{{ Theme::asset()->url('img/bathtub.svg') }}" width="13"
                         alt=""/>
                </div>
                {!! clean($property->number_bathroom) !!} {!! __('Bath') !!}
            </div>
            <div class="listing-card-info-icon">
                <div class="inc-fleat-icon">
                    <img src="{{ Theme::asset()->url('img/move.svg') }}" width="13" alt=""/>
                </div>
                {{ $property->square_text }}
            </div>
        </div>
    </div>

    <div class="listing-detail-footer">
        <div class="footer-first">
            <div class="foot-location d-flex">
                <img src="{{ Theme::asset()->url('img/pin.svg') }}" width="18"
                     alt="{!! clean($property->city_name ) !!}"/>
                {!! clean($property->city_name ) !!}
            </div>
        </div>
        <div class="footer-flex">
            <a href="{{ $property->url }}" class="prt-view">{{ __('View') }}</a>
        </div>
    </div>
</div>
