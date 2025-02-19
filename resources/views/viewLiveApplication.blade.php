@extends('include.app')

@section('header')
    <script src="{{ asset('asset/script/viewLiveApplication.js') }}"></script>
@endsection

@section('content')
    <div class="text-right mb-3">

    </div>
    <div class="card">
        <div class="card-header">
            <h4>{{ $data->user->fullname }}</h4>
            <?php if ($data->user->gender == 1) {
                echo "<span  class='badge bg-success text-white badge-shadow'>" . __('app.Male') . '</span>';
                // echo "<h2 class='btn btn-primary'>" . __('app.Male') . '</h2>';
            } else {
                echo "<span  class='badge bg-success text-white badge-shadow'>" . __('app.Female') . '</span>';
            }
            ?>
        </div>
        <div class="card-body">

            <div class="form-row ">
                @foreach ($data->user->images as $image)
                    <img class="rounded m-1 " src="{{ env('image') }}{{ $image->image }}" width="130" height="130">
                @endforeach
            </div>

            <button class="btn btn-primary my-3 text-white" id="view_video" data-bs-toggle="modal" data-bs-target=".fade" id="btn_videoIntro" rel="{{ $data->intro_video }}">Video Intro</button>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>{{ __('app.Full_Name') }}</label>
                    <input class="form-control" value="{{ $data->user->fullname }}" readonly>
                </div>

                <div class="form-group col-md-4">
                    <label>{{ __('app.Identity') }}</label>
                    <input class="form-control" value="{{ $data->user->identity }}" readonly>
                </div>

                <div class="form-group col-md-4">
                    <label>{{ __('app.Age') }}</label>
                    <input class="form-control" value="{{ $data->user->age }}" readonly>
                </div>

            </div>

            <div class="form-row">

                <div class="form-group col-md-12">
                    <label>{{ __('app.Languages') }}</label>
                    <input class="form-control" value="{{ $data->languages }}" readonly>
                </div>
            </div>


            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="product_description">{{ __('app.About') }} </label>
                    <textarea class="form-control" readonly>{{ $data->about_you }}</textarea>

                </div>
            </div>

            <div class="form-row">

                <div class="form-group col-md-12">
                    <label>{{ __('app.Social_links') }}</label>

                    <?php
                    $links = str_replace('"', '', $data->social_links);
                    
                    $links = explode(',', $links);
                    $count = count($links) - 1;
                    
                    for ($i = 0; $i <= $count; $i++) {
                        echo "<input class='form-control mt-2' value='$links[$i]' readonly>";
                    }
                    ?>

                </div>
            </div>


            <h2 class="btn btn-success" rel="{{ $data->id }}" id="btn_approve">Approve Application</h2>
            <h2 class="btn btn-danger" rel="{{ $data->id }}" id="btn_reject">Reject Application</h2>


        </div>
    </div>

    <div class="modal fade" id="video_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Video Intro</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <video rel="{{ $data->intro_video }}" id="video" width="450" height="450" controls>
                        <source src="https://www.w3schools.com/html/mov_bbb.mp4" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>

            </div>
        </div>
    </div>
@endsection
