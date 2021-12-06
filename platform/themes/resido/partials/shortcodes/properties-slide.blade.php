<!-- ============================ Latest Property For Sale Start ================================== -->
<section class="pt-0">
    <div class="container">

        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-10 text-center">
                <div class="sec-heading center mb-4">
                    <h2>{!! clean($title) !!}</h2>
                    <p>{!! clean($description) !!}</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="property-slide">
                    @foreach ($properties as $property)
                        <!-- Single Property -->
                        <div class="single-items">
                            {!! Theme::partial('real-estate.properties.item-grid', ['property' => $property, 'class_extend' => 'shadow-none border']) !!}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</section>
<!-- ============================ Latest Property For Sale End ================================== -->
