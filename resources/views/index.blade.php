@extends('include.app')
@section('header')
    <script src="{{ asset('asset/script/index.js') }}"></script>
@endsection

@section('content')
    <style>
        .chartjs-render-monitor {
            -webkit-animation: chartjs-render-animation 0.001s;
            animation: chartjs-render-animation 0.001s;
        }

        *,
        ::after,
        ::before {
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
        }

        .mainbg {
            background-image: linear-gradient(#FF6F43, #FE1B03) !important;
        }

        .card-icon2 {
            width: 50px;
            height: 50px;
            line-height: 50px;
            font-size: 22px;
            margin: 5px 0px;
            box-shadow: 2px 2px 10px 0 #97979794;
            border-radius: 10px;
            background: #ff5622;
            text-align: center;
        }

        .maincolor {
            color: white !important;
        }
    </style>

    <div class="row">
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <div class="card">
                <div class="card-statistic-4">
                    <div class="align-items-center justify-content-between">
                        <div class="row ">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pr-0 ">
                                <div class="card-icon2 mainbg">
                                    <i class="fas fa-user maincolor"></i>
                                </div>
                                <div class="card-content">
                                    <h5 class="font-15 mt-3">{{ __('All Users') }}</h5>
                                    <h3 class="mb-3 ">{{ $totalUsers }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <div class="card">
                <div class="card-statistic-4">
                    <div class="align-items-center justify-content-between">
                        <div class="row ">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pr-0 ">
                                <div class="card-icon2 mainbg">
                                    <i class="fas fa-user maincolor"></i>
                                </div>
                                <div class="card-content">
                                    <h5 class="font-15 mt-3">{{ __('Total Posts') }}</h5>
                                    <h3 class="mb-3 ">{{ $totalPosts }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <div class="card">
                <div class="card-statistic-4">
                    <div class="align-items-center justify-content-between">
                        <div class="row ">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pr-0 ">
                                <div class="card-icon2 mainbg">
                                    <i class="fas fa-camera-retro maincolor"></i>
                                </div>
                                <div class="card-content">
                                    <h5 class="font-15 mt-3">{{ __('Livestream Enabled Users') }}</h5>
                                    <h3 class="mb-3 ">{{ $liveStreamUsers }}</h3>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <div class="card">
                <div class="card-statistic-4">
                    <div class="align-items-center justify-content-between">
                        <div class="row ">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pr-0 ">
                                <div class="card-icon2 mainbg">
                                    <i class="fas fa-ban maincolor"></i>
                                </div>
                                <div class="card-content">
                                    <h5 class="font-15 mt-3">{{ __('Blocked Users') }}</h5>
                                    <h3 class="mb-3 ">{{ $blockedUsers }}</h3>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <div class="card">
                <div class="card-statistic-4">
                    <div class="align-items-center justify-content-between">
                        <div class="row ">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pr-0 ">
                                <div class="card-icon2 mainbg">
                                    <i class="fas fa-camera-retro maincolor"></i>
                                </div>
                                <div class="card-content">
                                    <h5 class="font-15 mt-3">{{ __('Live Applications') }}</h5>
                                    <h3 class="mb-3 ">{{ $liveApplications }}</h3>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <div class="card">
                <div class="card-statistic-4">
                    <div class="align-items-center justify-content-between">
                        <div class="row ">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pr-0 ">
                                <div class="card-icon2 mainbg">
                                    <i class="fas fa-user maincolor"></i>
                                </div>
                                <div class="card-content">
                                    <h5 class="font-15 mt-3">{{ __('Pending Redeems') }}</h5>
                                    <h3 class="mb-3 ">{{ $pendingRedeems }}</h3>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <div class="card">
                <div class="card-statistic-4">
                    <div class="align-items-center justify-content-between">
                        <div class="row ">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pr-0 ">
                                <div class="card-icon2 mainbg">
                                    <i class="fas fa-camera-retro maincolor"></i>
                                </div>
                                <div class="card-content">
                                    <h5 class="font-15 mt-3">{{ __('Completed Redeems') }}</h5>
                                    <h3 class="mb-3 ">{{ $completedRedeems }}</h3>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <div class="card">
                <div class="card-statistic-4">
                    <div class="align-items-center justify-content-between">
                        <div class="row ">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pr-0 ">
                                <div class="card-icon2 mainbg">
                                    <i class="fas fa-ban maincolor"></i>
                                </div>
                                <div class="card-content">
                                    <h5 class="font-15 mt-3">{{ __('Diamond Packs') }}</h5>
                                    <h3 class="mb-3 ">{{ $diamondPacks }}</h3>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <div class="card">
                <div class="card-statistic-4">
                    <div class="align-items-center justify-content-between">
                        <div class="row ">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pr-0 ">
                                <div class="card-icon2 mainbg">
                                    <i class="fas fa-camera-retro maincolor"></i>
                                </div>
                                <div class="card-content">
                                    <h5 class="font-15 mt-3">{{ __('Gifts') }}</h5>
                                    <h3 class="mb-3 ">{{ $gifts }}</h3>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <div class="card">
                <div class="card-statistic-4">
                    <div class="align-items-center justify-content-between">
                        <div class="row ">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pr-0 ">
                                <div class="card-icon2 mainbg">
                                    <i class="fas fa-user maincolor"></i>
                                </div>
                                <div class="card-content">
                                    <h5 class="font-15 mt-3">{{ __('Verification Requests') }}</h5>
                                    <h3 class="mb-3 ">{{ $verifyRequests }}</h3>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <div class="card">
                <div class="card-statistic-4">
                    <div class="align-items-center justify-content-between">
                        <div class="row ">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pr-0 ">
                                <div class="card-icon2 mainbg">
                                    <i class="fas fa-ban maincolor"></i>
                                </div>
                                <div class="card-content">
                                    <h5 class="font-15 mt-3">{{ __('Interests') }}</h5>
                                    <h3 class="mb-3 ">{{ $interests }}</h3>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <div class="card">
                <div class="card-statistic-4">
                    <div class="align-items-center justify-content-between">
                        <div class="row ">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pr-0 ">
                                <div class="card-icon2 mainbg">
                                    <i class="fas fa-camera-retro maincolor"></i>
                                </div>
                                <div class="card-content">
                                    <h5 class="font-15 mt-3">{{ __('Reports') }}</h5>
                                    <h3 class="mb-3 ">{{ $reports }}</h3>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


    </div>
@endsection
