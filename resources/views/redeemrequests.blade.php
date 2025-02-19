@extends('include.app')
@section('header')

    <script src="{{ asset('asset/script/redeemRequests.js') }}"></script>
@endsection
@section('content')

    <div class="card">
        <div class="card-header">
            <h4>{{ __('app.Redeem_Requests') }}</h4>
            <div class="ms-3 card-tab">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                     <li role="presentation" class="nav-item"><a class="nav-link pointer active" href="#Section1"
                            aria-controls="home" role="tab" data-toggle="tab">{{__('app.Pending_Requests')}}<span
                                class="badge badge-transparent total_open_complaint"></span></a>
                    </li>

                    <li role="presentation" class="nav-item"><a class="nav-link pointer" href="#Section2" role="tab"
                            data-toggle="tab">{{__('app.Completed_Requests')}}
                            <span class="badge badge-transparent total_close_complaint"></span></a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            <div class="tab  " role="tabpanel">
              
                <div class="tab-content tabs" id="home">
                    <div role="tabpanel" class="tab-pane active" id="Section1">
                        <table class="table table-striped w-100" id="table-pending">
                            <thead>
                                <tr>
                                    <th> {{ __('app.User_Image') }}</th>
                                    <th> {{ __('app.User') }}</th>
                                    <th> {{ __('app.Request_ID') }}</th>
                                    <th> {{ __('app.Coin_Amount') }}</th>
                                    <th> {{ __('app.Payable_Amount') }}</th>
                                    <th> {{ __('app.Payment_Gateway') }}</th>
                                    <th width="200px" style="text-align: right;"> {{ __('app.Action') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="Section2">
                        <table class="table table-striped w-100" width="100%" id="table-completed">
                            <thead>
                                <tr>
                                    <th> {{ __('app.User_Image') }}</th>
                                    <th> {{ __('app.User') }}</th>
                                    <th> {{ __('app.Request_ID') }}</th>
                                    <th> {{ __('app.Coin_Amount') }}</th>
                                    <th> {{ __('app.Amount_Paid') }}</th>
                                    <th> {{ __('app.Payment_Gateway') }}</th>
                                    <th width="200px" style="text-align: right;"> {{ __('app.Action') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="viewRequest" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">

                    <h4 id="request-id">{{ __('app.View_Redeem_Requests') }}</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form action="" method="post" enctype="multipart/form-data" class="add_category" id="completeForm"
                        autocomplete="off">
                        @csrf

                        <input type="hidden" class="form-control" id="editId" name="id" value="">

                            <div class="d-flex align-items-center">
                                <img id="user-img" class="mb-2 rounded-circle" src="http://placehold.jp/150x150.png" width="70" height="70">
                                <h5 id="user-fullname" class="m-2 "></h5>
                            </div>


                        <div class="form-group">
                            <label> {{ __('app.Coin_Amount') }}</label>
                            <input id="coin_amount" type="text" name="coin_amount" class="form-control" required readonly>
                        </div>

                        <div class="form-group">
                            <label> {{ __('app.Amount_Paid') }}</label>
                            <input id="amount_paid" type="text" name="amount_paid" class="form-control" required readonly>
                        </div>

                        <div class="form-group">
                            <label> {{ __('app.Payment_Gateway') }}</label>
                            <input id="payment_gateway" type="text" name="payment_gateway" class="form-control" required readonly>
                        </div>


                        <div class="form-group">
                            <label> {{ __('app.Account_details') }}</label>
                            <textarea id="account_details" type="text" name="account_details" class="form-control" required readonly></textarea>
                        </div>

                        <div id="div-submit" class="form-group d-none">
                            <input class="btn btn-success mr-1" type="submit" value=" {{ __('app.Complete') }}">
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>




@endsection
