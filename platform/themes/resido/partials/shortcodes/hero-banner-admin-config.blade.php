<div class="form-group mb-3">
    <label class="control-label">Title</label>
    <input name="title" value="{{ Arr::get($attributes, 'title') }}" class="form-control" placeholder="Find Accessible Homes To Rent">
</div>
<div class="form-group mb-3">
    <label class="control-label">Description</label>
    <textarea name="description" data-shortcode-attribute="content" class="form-control" rows="3">{{ $content }}</textarea>
</div>
<div class="form-group mb-3">
    <label class="control-label">Background</label>
    {!! Form::mediaImage('bg', Arr::get($attributes, 'bg')) !!}
</div>
<div class="form-group mb-3">
    <label class="control-label">Overlay</label>
    <input name="overlay" type="number" value="{{ Arr::get($attributes, 'overlay', '0') }}" class="form-control">
</div>
<div class="form-group mb-3">
    <label class="control-label">Style</label>
    <select name="style" class="form-control">
        @for($i = 1; $i < 10; $i++)
            <option value="{{ $i }}" @if (Arr::get($attributes, 'style') == $i) selected @endif>{{ $i }}</option>
        @endfor
    </select>
</div>
