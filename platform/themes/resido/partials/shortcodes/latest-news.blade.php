<section>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-10 text-center">
                <div class="sec-heading center">
                    <h2>{!! clean($title) !!}</h2>
                    <p>{!! clean($description) !!}</p>
                </div>
            </div>
        </div>
        <div class="row">
            @foreach ($posts as $post)
                <!-- Single blog Grid -->
                <div class="col-lg-4 col-md-6">
                    <div class="blog-wrap-grid">
                        <div class="blog-thumb">
                            <a href="{{ $post->url }}">
                                <img src="{{ RvMedia::getImageUrl($post->image, null, false, RvMedia::getDefaultImage()) }}"
                                    class="img-fluid" alt="{{ $post->name }}" />
                            </a>
                        </div>
                        <div class="blog-info">
                            <span class="post-date">
                                <i class="ti-calendar"></i>{{ $post->created_at->format('d M, Y') }}
                            </span>
                        </div>
                        <div class="blog-body">
                            <h4 class="bl-title">
                                <a href="{{ $post->url }}">{{ $post->name }}</a>
                            </h4>
                            <p>{{ $post->description }}</p>
                            <a href="{{ $post->url }}" class="bl-continue">{{ __('Continue') }}</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
<!-- ================= Blog Grid End ================= -->
