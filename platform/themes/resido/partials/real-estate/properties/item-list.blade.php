@php
    $is_lazyload = isset($lazyload) ? $lazyload : true;
@endphp
<div class="property-listing property-1" data-lat="{{ $property->latitude }}"
    data-long="{{ $property->longitude }}">
    <div class="listing-img-wrapper">
        <a href="{{ $property->url }}">
            @if($is_lazyload)
            <img src="{{ get_image_loading() }}"
                data-src="{{ RvMedia::getImageUrl($property->image ?? '', 'medium', false, RvMedia::getDefaultImage()) }}"
                class="img-fluid mx-auto lazy" alt="{{ $property->name }}"/>
            @else
            <img src="{{ RvMedia::getImageUrl($property->image ?? '', 'medium', false, RvMedia::getDefaultImage()) }}"
                class="img-fluid mx-auto" alt="{{ $property->name }}"/>
            @endif
        </a>
    </div>

    <div class="listing-content">

        <div class="listing-detail-wrapper-box">
            <div class="listing-detail-wrapper">
                <div class="listing-short-detail">
                    <h4 class="listing-name"><a href="{{ $property->url }}" title="{{ $property->name }}">{!! clean($property->name) !!}</a></h4>
                    
                </div>
                <div class="list-price">
                    <div>
                        @if (is_review_enabled() && $property->reviews_count > 0)
                        {!! Theme::partial('real-estate.elements.property-review', compact('property')) !!}
                        @endif
                        <span class="prt-types {{ $property->type->slug }}">{{ $property->type_name }}</span>
                    </div>
                    <h6 class="listing-card-info-price"> {{ $property->price_html }} </h6>
                </div>
            </div>
        </div>

        <div class="price-features-wrapper">
            <div class="list-fx-features">
                <div class="listing-card-info-icon">
                    <div class="inc-fleat-icon"><img src="{{ Theme::asset()->url('img/bed.svg') }}" width="13"
                            alt="" /></div>
                    {!! clean($property->number_bedroom) !!} {!! __('Beds') !!}
                </div>
                <div class="listing-card-info-icon">
                    <div class="inc-fleat-icon"><img src="{{ Theme::asset()->url('img/bathtub.svg') }}" width="13"
                            alt="" /></div>
                    {!! clean($property->number_bathroom) !!} {!! __('Bath') !!}
                </div>
                <div class="listing-card-info-icon">
                    <div class="inc-fleat-icon"><img src="{{ Theme::asset()->url('img/move.svg') }}" width="13"
                            alt="" /></div>
                    {{ $property->square_text }}
                </div>
            </div>
        </div>

        <div class="listing-footer-wrapper">
            <div class="listing-locate" title="{!! clean($property->city_name) !!}">
                <span class="listing-location"><i class="ti-location-pin"></i>{!! clean($property->city_name) !!}</span>
            </div>
            <div class="listing-detail-btn">
                <a href="{{ $property->url }}" class="more-btn">{{ __('View') }}</a>
            </div>
        </div>

    </div>
</div>
