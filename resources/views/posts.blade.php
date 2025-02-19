@extends('include.app')
@section('header')
    <script src="{{ asset('asset/script/post.js') }}"></script>
@endsection
@section('content')
    <div class="card">
        <div class="card-header">
            <h4>{{ __('app.Posts') }}</h4>
        </div>
        <div class="card-body">
           <table class="table table-striped w-100" id="postsTable">
                <thead>
                    <tr>
                        <th style="width: 150px"> {{ __('Content') }} </th>
                        <th> {{ __('User name') }} </th>
                        <th> {{ __('Full name') }} </th>
                        <th> {{ __('Comments') }} </th>
                        <th> {{ __('Likes') }} </th>
                        <th> {{ __('Created At') }} </th>
                        <th style="text-align: right; width: 200px;"> {{ __('Action') }} </th>
                    </tr>
                </thead>
            </table> 
        </div>
    </div>
    
        <!-- View Post Modal -->
    <div class="modal fade" id="viewPostModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 fw-normal" id="exampleModalLabel">View Post</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label> {{ __('Post Description') }}</label>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Post Desc Modal -->
    <div class="modal fade" id="viewPostDescModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 fw-normal" id="exampleModalLabel">View Post</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group m-0">
                        <label> {{ __('postDescription') }}</label>
                        <p class="m-0" id="postDesc1"> </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div> 
@endsection
