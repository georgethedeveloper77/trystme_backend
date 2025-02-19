@extends('include.app')
@section('header')
<script src="{{ asset('asset/script/story.js') }}"></script>
<script src="{{ asset('asset/script/env.js') }}"></script>
@endsection

@section('content')
<section class="section">

    <div class="card">
        <div class="card-header">
            <div class="page-title w-100">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 fw-normal d-flex align-items-center"> {{ __('app.allStories') }} </h4>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-striped w-100" id="allStoriesTable">
                <thead>
                    <tr>
                        <th width="100px"> {{ __('app.content')}} </th>
                        <th width="100px"> {{ __('app.fullname')}} </th>
                        <th width="100px"> {{ __('app.time')}} </th>
                        <th width="250px" style="text-align: right"> {{ __('app.action')}} </th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

</section>
<!-- View Story Modal -->
<div class="modal fade" id="viewStoryModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-normal" id="exampleModalLabel">{{ __('app.viewStory') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group position-relative m-0">
                    <label class="form-label"> {{ __('app.story') }}</label>
                    <img src="" alt="" srcset="" id="story_content" class="img-fluid story_content_view">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('app.close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- View Story Modal -->
<div class="modal fade" id="viewStoryVideoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-normal" id="exampleModalLabel">{{ __('app.viewStoryVideo') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group position-relative m-0">
                    <label class="form-label"> {{ __('app.story') }}</label>
                    <video controls id="story_content_video" class="img-fluid story_content_view">

                    </video>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('app.close') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection