@php
$categories = app(Botble\RealEstate\Repositories\Interfaces\CategoryInterface::class)->all();
@endphp
<select id="ptypes" data-placeholder="{{ __('Category') }}" name="category_id" class="form-control">
    <option value="">&nbsp;</option>
    @foreach ($categories as $category)
        <option value="{{ $category->id }}" @if (request()->input('category_id') == $category->id) selected @endif>
            {{ $category->name }} </option>
    @endforeach
</select>
