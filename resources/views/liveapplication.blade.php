@extends('include.app')
@section('header')
    <script src="{{ asset('asset/script/liveApplication.js') }}"></script>
@endsection
@section('content')

<div class="card">
    <div class="card-header">
        <h4>{{ __('app.Live_applications') }}</h4>
    </div>
    <div class="card-body">
        <table class="table table-striped w-100" id="liveApplicationTable">
            <thead>
                <tr>
                    <th> {{ __('app.User_Image') }}</th>
                    <th> {{ __('app.User') }}</th>
                    <th> {{ __('app.Languages') }}</th>
                    <th width="200px" style="text-align: right"> {{ __('app.Action') }}</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

@endsection