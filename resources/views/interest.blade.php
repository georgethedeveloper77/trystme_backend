@extends('include.app')

@section('header')
    <script src="{{ asset('asset/script/interest.js') }}"></script>
@endsection

@section('content')
   
    <div class="card">
        <div class="card-header">
            <h4>{{ __('app.Interests') }}</h4>
            <a class="btn btn-primary InterestsaddModalbtn ml-auto" href="" data-bs-toggle="modal"
                data-bs-target="#addInterest">{{ __('app.AddInterest') }}
            </a>
        </div>
        <div class="card-body">
            <table class="table table-striped w-100" id="interestTable">
                <thead>
                    <tr>
                        <th>{{ __('app.Title') }}</th>
                        <th class="text-end" width="200px">{{ __('app.Action') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="addInterest" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class=" ">
                        <h5>{{ __('app.AddInterest') }} </h5>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" enctype="multipart/form-data" class="add_category" id="addForm" autocomplete="off">
                        @csrf
                        <div class="form-group">
                            <label>{{ __('app.Title') }} </label>
                            <input type="text" id="cat_title" name="title" class="form-control" required>
                        </div>
                        <div class="form-group m-0">
                            <input class="btn btn-primary mr-1" type="submit" id="addcat2" value="{{ __('app.Save') }}">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editInterestModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('app.EditInterest') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" enctype="multipart/form-data" id="editInterestForm" autocomplete="off">

                        @csrf
                        <input type="hidden" class="form-control" id="editInterstId" name="id" value="">

                        <div class="form-group">
                            <label for="">{{ __('app.Title') }}</label>
                            <input type="text" class="form-control" id="editInterstTitle" name="title" required>
                        </div>


                        <div class="form-group text-right m-0">
                            <input type="submit" value="{{ __('app.Save') }}" class=" btn btn-primary" id="editcat2">
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection
