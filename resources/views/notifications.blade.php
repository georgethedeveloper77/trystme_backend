@extends('include.app')
@section('header')
<script src="{{ asset('asset/script/notification.js') }}"></script>
@endsection
@section('content')
<div class="card">
    <div class="card-header">
        <h4>{{ __('app.Notifications') }}</h4>
        <a class="btn btn-primary addModalBtn ml-auto" data-bs-toggle="modal" data-bs-target="#addNotificationModal" href="">{{ __('app.Send_notification') }}
        </a>
    </div>
    <div class="card-body">
        <table class="table table-striped w-100" id="notificationTable">
            <thead>
                <tr>
                    <th> {{ __('app.Title') }}</th>
                    <th> {{ __('app.Message') }}</th>
                    <th width="200px" style="text-align: right;"> {{ __('app.Action') }}</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<div class="modal fade" id="addNotificationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">

                <h5>{{ __('app.Send_notification') }}</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <form action="" method="post" enctype="multipart/form-data" class="add_category" id="addForm" autocomplete="off">
                    @csrf
                    <div class="form-group">
                        <label> {{ __('app.Title') }}</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>


                    <div class="form-group">
                        <label> {{ __('app.Message') }}</label>
                        <textarea type="text" name="message" class="form-control" required></textarea>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn-primary mr-1 button1 saveButton btn theme-btn saveButton"> {{ __('app.Send_notification') }} </button>
                        <!-- <input class="btn " type="submit" value=" "> -->
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>


<div class="modal fade" id="editNotificationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"> {{ __('app.EditPackage') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post" enctype="multipart/form-data" id="editNotificationForm" autocomplete="off">

                    @csrf
                    <input type="hidden" class="form-control" id="notificationID" name="id" value="">

                    <div class="form-group">
                        <label>{{ __('app.Title') }}</label>
                        <input type="text" name="title" id="editNotificationTitle" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label> {{ __('app.Message') }}</label>
                        <textarea type="text" id="editNotificationMessage" name="message" class="form-control" required></textarea>
                    </div>


                    <div class="form-group">
                        <input type="submit" class=" btn btn-primary">
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
@endsection