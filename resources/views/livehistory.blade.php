@extends('include.app')
@section('header')

    <script src="{{ asset('asset/script/liveHistory.js') }}"></script>
@endsection
@section('content')

    <div class="card">
        <div class="card-header">
            <h4>{{ __('app.Live_History') }}</h4>
        </div>
        <div class="card-body">
            <table class="table table-striped w-100" id="table-22">
                <thead>
                    <tr>
                        <th> {{ __('app.User_Image') }}</th>
                        <th> {{ __('app.User') }}</th>
                        <th> {{ __('app.Started_At') }}</th>
                        <th> {{ __('app.Streamed_For') }}</th>
                        <th> {{ __('app.Coins_Collected') }}</th>
                        <th> {{ __('app.Date') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection
