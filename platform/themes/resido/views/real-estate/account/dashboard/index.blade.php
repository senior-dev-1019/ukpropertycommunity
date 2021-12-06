@extends(Theme::getThemeNamespace() . '::views.real-estate.account.master')
@php
$user = auth('account')->user();
@endphp
@section('content')
    {!! apply_filters(ACCOUNT_TOP_STATISTIC_FILTER, null) !!}

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <h4>{{ __('Your Current Credits') }}: <span class="pc-title theme-cl">{{ auth('account')->user()->credits }}</span></h4>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="dashboard-stat widget-1">
                <div class="dashboard-stat-content">
                    <h4>{{ $user->properties()->where('moderation_status', \Botble\RealEstate\Enums\ModerationStatusEnum::APPROVED)->count() }}</h4>
                    <span>{{ trans('plugins/real-estate::dashboard.approved_properties') }}</span>
                </div>
                <div class="dashboard-stat-icon"><i class="ti-location-pin"></i></div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="dashboard-stat widget-2">
                <div class="dashboard-stat-content">
                    <h4>{{ $user->properties()->where('moderation_status', \Botble\RealEstate\Enums\ModerationStatusEnum::PENDING)->count() }}</h4>
                    <span>{{ trans('plugins/real-estate::dashboard.pending_approve_properties') }}</span></div>
                <div class="dashboard-stat-icon"><i class="ti-pie-chart"></i></div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="dashboard-stat widget-3">
                <div class="dashboard-stat-content">
                    <h4>{{ $user->properties()->where('moderation_status', \Botble\RealEstate\Enums\ModerationStatusEnum::REJECTED)->count() }}</h4>
                    <span>{{ trans('plugins/real-estate::dashboard.rejected_properties') }}</span>
                </div>
                <div class="dashboard-stat-icon"><i class="ti-user"></i></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div id="app-real-estate">
            <activity-log-component default-active-tab="activity-logs"></activity-log-component>
        </div>
    </div>
@endsection
