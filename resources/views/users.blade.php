@extends('include.app')
@section('header')
    <script src="{{ asset('asset/script/users.js') }}"></script>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <h4>{{ __('app.Users') }}</h4>
                <div class="ms-3 card-tab">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li role="presentation" class="nav-item">
                        <a class="nav-link pointer active" href="#Section1"
                            aria-controls="home" role="tab" data-toggle="tab">{{ __('app.All_Users') }}<span
                                class="badge badge-transparent total_open_complaint"></span></a>
                    </li>

                    <li role="presentation" class="nav-item"><a class="nav-link pointer" href="#Section2" role="tab"
                            data-toggle="tab">{{ __('app.Streamers') }}
                            <span class="badge badge-transparent total_close_complaint"></span></a>
                    </li>

                    <li role="presentation" class="nav-item"><a class="nav-link pointer" href="#Section3" role="tab"
                            data-toggle="tab">{{ __('app.Fake_Users') }}
                            <span class="badge badge-transparent total_close_complaint"></span></a>
                    </li>
                    </ul>
                </div>
            </div>
            <a href="{{ route('addFakeUser') }}" id="add-fake-user" class="ml-auto btn btn-primary">{{ __('app.Add_fake_user') }}</a>
        </div>

        <div class="card-body">

            <div class="tab" role="tabpanel">
                <div class="tab-content tabs" id="home">
                    {{-- Section 1 --}}
                    <div role="tabpanel" class="tab-pane active" id="Section1">
                        <table class="table table-striped w-100" id="UsersTable">
                            <thead>
                                <tr>
                                    <th>{{ __('app.User_Image') }}</th>
                                    <th>{{ __('app.Identity') }}</th>
                                    <th>{{ __('app.Full_Name') }}</th>
                                    <th>{{ __('app.Wallet') }}</th>
                                    <th>{{ __('app.Live_eligible') }}</th>
                                    <th>{{ __('app.Age') }}</th>
                                    <th>{{ __('app.Gender') }}</th>
                                    <th>{{ __('app.BlockUser') }}</th>
                                    <th>{{ __('app.ViewDetails') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    {{-- Section 2 --}}
                    <div role="tabpanel" class="tab-pane" id="Section2">
                        <table class="table table-striped w-100" id="StreamersTable">
                            <thead>
                                <tr>
                                    <th>{{ __('app.User_Image') }}</th>
                                    <th>{{ __('app.Identity') }}</th>
                                    <th>{{ __('app.Full_Name') }}</th>
                                    <th>{{ __('app.Live_eligible') }}</th>
                                    <th>{{ __('app.Age') }}</th>
                                    <th>{{ __('app.Gender') }}</th>
                                    <th>{{ __('app.BlockUser') }}</th>
                                    <th>{{ __('app.ViewDetails') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    {{-- Section 3 --}}
                    <div role="tabpanel" class="tab-pane" id="Section3">
                        <table class="table table-striped w-100" id="FakeUsersTable">
                            <thead>
                                <tr>
                                    <th>{{ __('app.User_Image') }}</th>
                                    <th>{{ __('app.Full_Name') }}</th>
                                    <th>{{ __('app.Identity') }}</th>
                                    <th>{{ __('app.Password') }}</th>
                                    <th>{{ __('app.Age') }}</th>
                                    <th>{{ __('app.Gender') }}</th>
                                    <th>{{ __('app.BlockUser') }}</th>
                                    <th>{{ __('app.ViewDetails') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add coins Modal --}}
    <div class="modal fade" id="addCoinsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">

                <h5>{{ __('Add Coins') }}</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <form action="" method="post" enctype="multipart/form-data" class="add_category" id="addCoinsForm"
                    autocomplete="off">
                    @csrf

                    <input type="hidden" name="id" id="userId" value="">

                    <div class="form-group">
                        <label for="coins">Coins</label>
                        <input required type="number" class="form-control" id="coins" name="coins"
                            placeholder="Enter Coin Amount">
                    </div>

                    <div class="form-group">
                        <input class="btn btn-primary mr-1" type="submit" value=" {{ __('app.Submit') }}">
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
@endsection
