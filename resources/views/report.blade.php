@extends('include.app')
@section('header')
    <script src="{{ asset('asset/script/report.js') }}"></script>
@endsection
@section('content')
    <div class="card">
        <div class="card-header">
            <h4>{{ __('app.Reports') }}</h4>
            <div class="ms-3 card-tab">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="userReportTab" data-bs-toggle="tab"
                            data-bs-target="#userReport-pane" type="button" role="tab"
                            aria-controls="userReport-panel" aria-selected="true">  {{ __('User reports')}} </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="postReport-tab" data-bs-toggle="tab"
                        data-bs-target="#postReport-pane" type="button" role="tab"
                        aria-controls="postReport-pane" aria-selected="false">  {{ __('Post reports')}} </button>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane show active" id="userReport-pane" role="tabpanel" tabindex="0">
                    <table class="table table-striped w-100" style="width: 100% !important;" id="UsersTable">
                        <thead>
                            <tr>
                                <th>{{ __('app.Image') }} </th>
                                <th>{{ __('app.Identity') }} </th>
                                <th>{{ __('app.Full_Name') }} </th>
                                <th>{{ __('app.Reason') }}</th>
                                <th>{{ __('app.Description') }}</th>
                                <th width="200px" style="text-align: right;">{{ __('app.Action') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="tab-pane" id="postReport-pane" role="tabpanel" tabindex="0">
                    <table class="table table-striped w-100" id="fetchPostReport">
                        <thead>
                            <tr>
                                <th style="width: 200px"> {{ __('Post')}} </th>
                                <th style="width: 300px"> {{ __('Reason')}} </th>
                                <th> {{ __('Description')}} </th>
                                <th style="text-align: right; width: 350px;"> {{ __('Action')}} </th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>


      <!-- View Post Modal -->
    <div class="modal fade" id="viewPostModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 fw-normal" id="exampleModalLabel"> {{ __('View post')}} </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label> {{ __('Post description') }}</label>
                        <p id="postDesc"> </p>
                    </div>
                    <hr>
                    <div class="form-group position-relative">
                        <label> {{ __('Post') }}</label>
                        <div class="swiper-container mySwiper">
                            <div id="post_contents">
                            </div>
                        </div>
                        <div class="swiper-button swiper-button-prev"></div>
                        <div class="swiper-button swiper-button-next"></div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"> {{ __('Close')}} </button>
                </div>
            </div>
        </div>
    </div>

      <!-- View Post Desc Modal -->
      <div class="modal fade" id="viewPostDescModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 fw-normal" id="exampleModalLabel">{{ __('View post')}}</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group m-0">
                        <label> {{ __('Post description') }}</label>
                        <p class="m-0" id="postDesc1"> </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close')}}</button>
                </div>
            </div>
        </div>
    </div>
    
@endsection
