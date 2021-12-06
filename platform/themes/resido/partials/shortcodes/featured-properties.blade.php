<section class="bg-light">
    <div class="container">

        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-10 text-center">
                <div class="sec-heading center">
                    <h2>{!! clean($title) !!}</h2>
                    <p>{!! clean($description) !!}</p>
                </div>
            </div>
        </div>

        <div class="row list-layout">
            @foreach($properties as $property)
            <!-- Single Property -->
            @if($style == 1)
            <div class="col-lg-6 col-sm-12">
                {!! Theme::partial('real-estate.properties.item-list', compact('property')) !!}
            @else
            <div class="col-lg-4 col-md-6 col-sm-12">
                {!! Theme::partial('real-estate.properties.item-grid', compact('property')) !!}
            @endif
            </div>
            <!-- End Single Property -->
            @endforeach
        </div>

        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 text-center">
                <a href="{{ route('public.properties') }}" class="btn btn-theme-light-2 rounded">{{ __('Browse More Properties') }}</a>
            </div>
        </div>
    </div>
</section>
