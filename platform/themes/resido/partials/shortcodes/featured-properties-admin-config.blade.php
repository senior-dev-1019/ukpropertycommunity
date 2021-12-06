<div class="form-group mb-3">
    <label class="control-label">Title</label>
    <input name="title" value="{{ Arr::get($attributes, 'title') }}" class="form-control">
</div>

<div class="form-group mb-3">
    <label class="control-label">Description</label>
    <textarea name="description" data-shortcode-attribute="content" class="form-control" rows="3">{{ $content }}</textarea>
</div>

<div class="form-group mb-3">
    <label class="control-label">Limit</label>
    <input name="limit" class="form-control" value="{{ Arr::get($attributes, 'limit', 6) }}">
</div>
<div class="form-group mb-3">
    <label class="control-label">Style</label>
    <select name="style" class="form-control">
        <option value="1" @if (Arr::get($attributes, 'style') == 1) selected @endif>Style 1</option>
        <option value="2" @if (Arr::get($attributes, 'style') == 2) selected @endif>Style 2</option>
    </select>
</div>
