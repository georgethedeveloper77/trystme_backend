@extends('include.app')
@section('header')
    <script src="{{ asset('asset/script/package.js') }}"></script>
@endsection
@section('content')
    <div class="modal fade" id="addcat" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">

                    <h5>{{ __('app.Add_Subscriptions') }}</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form action="" method="post" enctype="multipart/form-data" class="add_category" id="addForm"
                        autocomplete="off">
                        @csrf
                        <div class="form-group">
                            <label> {{ __('app.Title') }}</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>


                        <div class="form-group">
                            <label> {{ __('app.Description') }}</label>
                            <input type="text" name="description" class="form-control" required>
                        </div>


                        <div class="form-group">
                            <label> {{ __('app.Price') }}</label>
                            <input type="text" name="price" class="form-control" required>
                        </div>



                        <div class="form-group">
                            <label> {{ __('app.Months') }}</label>
                            <input type="number" name="months" class="form-control" required>
                        </div>



                        <div class="form-group">
                            <label>{{ __('app.Playstoreid') }}</label>
                            <input type="text" name="playid" class="form-control" required>
                        </div>


                        <div class="form-group">
                            <label> {{ __('app.Appstoreid') }}</label>
                            <input type="text" name="appid" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <input class="btn btn-primary mr-1" type="submit" value=" {{ __('app.Add_Subscriptions') }}">
                        </div>

                    </form>




                </div>

            </div>
        </div>
    </div>


    <div class="card">
        <div class="card-header">
            <h4>{{ __('app.Subscriptions') }}</h4>
            <a class="btn btn-primary addModalBtn ml-auto" data-bs-toggle="modal" data-bs-target="#addcat"
                href="">{{ __('app.Add_Subscriptions') }}
            </a>
        </div>
        <div class="card-body">
                <table class="table table-striped w-100" id="table-22">
                    <thead>
                        <tr>

                            <th> {{ __('app.Title') }}</th>
                            <th> {{ __('app.Description') }}</th>
                            <th> {{ __('app.Price') }}</th>
                            <th> {{ __('app.Months') }}</th>
                            <th> {{ __('app.Appstoreid') }}</th>
                            <th> {{ __('app.Playstoreid') }}</th>
                            <th> {{ __('app.Action') }}</th>
                        </tr>
                    </thead>
                </table>
        </div>
    </div>


    <div class="modal fade" id="edit_cat_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> {{ __('app.Edit_Subscriptions') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" enctype="multipart/form-data" id="edit_cat" autocomplete="off">

                        @csrf
                        <input type="hidden" class="form-control" id="editId" name="id" value="">

                        <div class="form-group">
                            <label>{{ __('app.Title') }}</label>
                            <input type="text" name="title" id="title" class="form-control" required>
                        </div>


                        <div class="form-group">
                            <label> {{ __('app.Description') }}</label>
                            <input type="text" name="description" id="description" class="form-control" required>
                        </div>


                        <div class="form-group">
                            <label> {{ __('app.Price') }}</label>
                            <input type="text" name="price" id="price" class="form-control" required>
                        </div>



                        <div class="form-group">
                            <label> {{ __('app.Months') }}</label>
                            <input type="number" name="months" id="months" class="form-control" required>
                        </div>



                        <div class="form-group">
                            <label> {{ __('app.Playstoreid') }}</label>
                            <input type="text" name="playid" id="playid" class="form-control" required>
                        </div>


                        <div class="form-group">
                            <label>{{ __('app.Appstoreid') }} </label>
                            <input type="text" name="appid" id="appid" class="form-control" required>
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
