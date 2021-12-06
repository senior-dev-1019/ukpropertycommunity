@extends('core/base::layouts.master')
@section('content')
    <div class="container">
        <h1 class="text-center" style="padding-top: 20px;">{{ trans('core/base::system.updater') }}</h1><br>
        <div class="updater-box">
            @if ($updateData['status'])
                <p class="note note-warning">
                    Please backup your database and script files before upgrading.
                </p>
            @endif
            <p class="mb-0">
                {{ $updateData['message'] }}
            </p>
            <div class="content">
                @if ($updateData['status'])
                    <br>
                    <div class="note note-info">
                        {!! trim($updateData['changelog']) !!}
                    </div>
                    <br>
                    @if (request()->input('update_id'))
                        @php
                            $updateId = strip_tags(trim(request()->input('update_id')));
                            $version = strip_tags(trim(request()->input('version')));
                            echo '<progress id="prog" value="0" max="100.0" class="progress is-success" style="margin-bottom: 10px;"></progress>';
                            $api->downloadUpdate($updateId, $version);
                        @endphp
                    @else
                        <form action="{{ route('system.updater') }}" method="POST">
                            @csrf
                            <input type="hidden" class="form-control" value="{{ $updateData['update_id'] }}" name="update_id">
                            <input type="hidden" class="form-control" value="{{ $updateData['version'] }}" name="version">
                            <p class="text-center">
                                <button type="submit" class="btn btn-warning btn-update-new-version" data-updating-text="Updating..."><i class="fa fa-download"></i> <span>Download & Install Update</span></button>
                            </p>
                        </form>
                    @endif
                @endif
            </div>
        </div>
    </div>
@stop
