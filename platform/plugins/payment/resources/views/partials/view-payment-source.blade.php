<button class="btn btn-primary my-2" type="button" data-bs-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
    {{ trans('plugins/payment::payment.view_response_source')}}
</button>
<div class="collapse" id="collapseExample">
    <div class="card card-body p-0">
        <code class="p-2">
            <pre>@php  print_r($payment); @endphp</pre>
        </code>
    </div>
</div>
