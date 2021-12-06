<!-- ============================ Hero Banner  Start================================== -->
<!-- <div class="featured_slick_gallery gray">
    {!! Theme::partial('real-estate.properties.slick-gallery', compact('property')) !!}
</div> -->
<!-- ============================ Hero Banner End ================================== -->

<div class="container">
    <div class="row p-4">
        @foreach( $property->images as $index => $image)
            <div class="col-md-4 mb-4">
                <a href="{{ RvMedia::getImageUrl($image, null, false, RvMedia::getDefaultImage()) }}" class="mfp-gallery">
                    <img src="{{ RvMedia::getImageUrl($image, 'property_large', false, RvMedia::getDefaultImage()) }}"
                         class="img-fluid mx-auto" alt="{{ $property->name }}-{{ $index }}" />
                </a>
            </div>
        @endforeach
    </div>
</div>