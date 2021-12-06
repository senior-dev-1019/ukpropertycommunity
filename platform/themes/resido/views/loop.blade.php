@if ($posts->count() > 0)
    <div class="row">
        @foreach($posts as $post)
            <div class="col-lg-4 col-md-6">
                <div class="blog-wrap-grid">
                    <div class="blog-thumb">
                        <a href="{{ $post->url }}">
                            <img
                                data-src="{{ RvMedia::getImageUrl($post->image, 'medium', false, RvMedia::getDefaultImage()) }}"
                                src="{{ get_image_loading() }}"
                                alt="{{ $post->name }}" class="img-fluid thumb lazy">
                        </a>
                    </div>

                    <div class="blog-info">
                        {!! Theme::partial('post-meta', compact('post')) !!}
                    </div>

                    <div class="blog-body">
                        <h4 class="bl-title">
                            <a href="{{ $post->url }}" title="{{ $post->name }}">
                                {{ $post->name }}
                            </a>
                        </h4>
                        <p>{{ Str::words($post->description, 50) }}</p>
                        <a href="{{ $post->url }}" class="bl-continue">{{ __('Continue') }}</a>
                    </div>

                </div>
            </div>
        @endforeach
    </div>
    <br>

    <div class="colm10 col-sm-12">
        <nav class="d-flex justify-content-center pt-3">
            {!! $posts->withQueryString()->links() !!}
        </nav>
    </div>
@endif
