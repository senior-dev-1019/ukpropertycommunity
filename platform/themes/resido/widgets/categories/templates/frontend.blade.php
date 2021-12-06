<!-- Categories -->
<div class="single-widgets widget_category">
    <h4 class="title">{{ $config['name'] }}</h4>
    <ul>
        @foreach(get_categories(['select' => ['categories.id', 'categories.name']]) as $category)
            <li><a href="{{ $category->url }}" class="text-dark">{{ $category->name }}</a></li>
        @endforeach
    </ul>
</div>

