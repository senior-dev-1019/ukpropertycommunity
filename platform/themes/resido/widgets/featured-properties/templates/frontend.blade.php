<div class="sidebar_featured_property">
    <h4 class="title">{{ $config['name'] }}</h4>
    @foreach (get_featured_properties($config['number_display']) as $property)
        <div class="sides_list_property">
            <div class="sides_list_property_thumb">
                <img src="{{ RvMedia::getImageUrl($property->image, 'thumb', false, RvMedia::getDefaultImage()) }}" class="img-fluid" alt="{{ $property->name }}">
            </div>
            <div class="sides_list_property_detail">
                <h4><a href="{{ $property->url }}">{{ $property->name }}</a></h4>
                <span><i class="ti-location-pin"></i>{{ $property->location }}</span>
                <div class="lists_property_price">
                    <div class="lists_property_types">
                        <div class="property_types_vlix {{ $property->type_slug }}">{{ $property->type_name }}</div>
                    </div>
                    <div class="lists_property_price_value">
                        <h4>{{ $property->price_html }}</h4>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
