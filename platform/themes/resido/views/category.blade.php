<!-- ============================ Page Title Start================================== -->
<div class="page-title">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <h2 class="ipt-title">{{ $category->name }}</h2>
                <span class="ipn-subtitle">{{ $category->description }}</span>
            </div>
        </div>
    </div>
</div>
<!-- ============================ Page Title End ================================== -->

<section class="gray-simple">
    <div class="container">
        <div class="row">
            <div class="col text-center">
                <div class="sec-heading center">
                    {!! Theme::partial('breadcrumb') !!}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-9">
                <div class="row">
                    @foreach($posts as $post)
                        <div class="col-lg-4 col-md-6">
                            <div class="blog-wrap-grid">
                                <div class="blog-thumb">
                                    <a href="{{ $post->url }}">
                                        <img
                                            data-src="{{ RvMedia::getImageUrl($post->image, 'medium', false, RvMedia::getDefaultImage()) }}"
                                            src="{{ RvMedia::getImageUrl($post->image, 'medium', false, RvMedia::getDefaultImage()) }}"
                                            alt="{{ $post->name }}" class="img-fluid thumb">
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
                                    <a href="{{ $post->url }}" class="bl-continue">Continue</a>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-sm-3">
                <div class="blog-wrap-grid"></div>
                {!! dynamic_sidebar('primary_sidebar') !!}
            </div>
        </div>
        <br>

        <div class="colm10 col-sm-12">
            <nav class="d-flex justify-content-center pt-3">
                {!! $posts->withQueryString()->links() !!}
            </nav>
        </div>
    </div>
</section>

