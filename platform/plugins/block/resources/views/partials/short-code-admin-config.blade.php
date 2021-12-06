<div class="form-group">
    <label class="control-label">{{ trans('plugins/block::block.static_block_short_code_name') }}</label>
    {!! Form::customSelect('alias', $blocks, Arr::get($attributes, 'alias')) !!}
</div>
