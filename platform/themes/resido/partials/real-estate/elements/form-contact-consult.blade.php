{!! Form::open(['route' => 'public.send.consult', 'method' => 'POST', 'class' => 'contact-form', 'id' => 'contactForm']) !!}
<div class="row">
    <input type="hidden" name="data_id" value="{{ $data->id }}">
    <div class="form-group">
        <input class="form-control" name="name" id="name" type="text" placeholder="{{ __('Name') }} *" required>
    </div>
    <div class="form-group">
        <input type="text" name="phone" class="form-control" placeholder="{{ __('Phone') }} *"
            data-validation-engine="validate[required]"
            data-errormessage-value-missing="{{ __('Please enter phone number') }}!">
    </div>
    <div class="form-group">
        <input class="form-control" name="email" id="email" type="email" placeholder="{{ __('Email') }}">
    </div>
    <div class="form-group">
        <input type="text" class="form-control" placeholder="{{ __('Subject') }} *" value="{{ $data->name }}"
            readonly>
    </div>
    <div class="form-group">
        <textarea name="content" class="form-control" rows="5" placeholder="{{ __('Message') }}"></textarea>
    </div>
    @if (setting('enable_captcha') && is_plugin_active('captcha'))
        <div class="form-group">
            {!! Captcha::display() !!}
        </div>
    @endif
    <div class="form-group">
        <button class="btn btn-black btn-md rounded full-width" type="submit">{{ __('Send Message') }}</button>
    </div>
    <div class="clearfix"></div>
    <br>
    <div class="alert alert-success text-success text-left" style="display: none;">
        <span></span>
    </div>
    <div class="alert alert-danger text-danger text-left" style="display: none;">
        <span></span>
    </div>
</div>


{!! Form::close() !!}
