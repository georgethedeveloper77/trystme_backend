@extends('include.app')
@section('header')
<script src="{{ asset('asset/script/addfakeuser.js') }}"></script>
<link rel="stylesheet" href="{{ asset('asset/style/addFakeUser.css') }}">
@endsection

@section('content')
<style>
    .iconformusic i {
        font-size: 40px !important;
        margin-top: 5px;
        color: black;

    }

    .dropbtn {
        width: 100%;
        background-color: #3e8e41;
        cursor: pointer;
        padding: 10px;
    }



    #myInput {
        box-sizing: border-box;

        font-size: 14px;
        padding-left: 25px;
        border: none;
        width: 100%;

    }

    #myArtInput {
        box-sizing: border-box;

        font-size: 14px;
        padding-left: 25px;
        border: none;
        width: 100%;

    }

    #myInput {
        outline: none;
    }

    #myArtInput:focus {
        outline: none;
    }

    .dropdown {
        position: relative;
        display: inline-block;
        width: 100%;


    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #f6f6f6;
        width: 100%;
        border-radius: 5px;
        overflow: hidden;
        margin-top: -58px;
        border: 1px solid #ddd;
        max-height: 250px;
        z-index: 1;
    }

    .dropdown-content option {
        color: black;
        padding: 5px 16px;
        text-decoration: none;
        display: block;
    }

    .hk::-webkit-scrollbar-track {
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
        background-color: #F5F5F5;
    }

    .hk::-webkit-scrollbar {
        width: 10px;
        background-color: #F5F5F5;
    }

    .hk::-webkit-scrollbar-thumb {
        background-color: #4F5ECE;
        background-image: -webkit-linear-gradient(45deg,
                rgba(255, 255, 255, .2) 25%,
                transparent 25%,
                transparent 50%,
                rgba(255, 255, 255, .2) 50%,
                rgba(255, 255, 255, .2) 75%,
                transparent 75%,
                transparent)
    }



    .show {
        display: block;
    }

    .active {
        background-color: #4F5ECE;
        color: white !important;
        border-radius: 10px;
    }

    .closeicon {
        font-size: 20px;
        padding: 10px;
        background-color: white;
        color: black !important;

    }

    .closeArtistIcon {
        font-size: 20px;
        padding: 10px;
        background-color: white;
        color: black !important;

    }

    .hk {
        overflow-y: scroll;
        padding: 0px 10px;
        max-height: 200px;
    }

    .list li {
        list-style: none;
        padding: 6px 10px;
        background-color: #4F5ECE;
        margin-right: 10px;
        color: white !important;
        border-radius: 5px;
        /* background-color: #4F5ECE; */
        width: fit-content;
    }

    .list li i {
        margin-left: 10px;
    }
</style>

<div class="card">
    <div class="card-header">
        <h4>
            {{ __('Add Fake User') }}
        </h4>

    </div>

    <div class="card-body">

        <form action="" method="post" enctype="multipart/form-data" class="addFakeUser" id="addForm" autocomplete="off">
            @csrf

            {{-- Image part --}}
            <div id="photo_gallery2" class="w-100 ml-1 d-flex">

            </div>

            <div id="imgInput" class="form-group">
                <label for="productimages">
                    <a class="btn btn-primary text-white">{{ __('Add Image') }}</a>
                </label>
                <input type="file" class="form-control d-none" id="productimages" name="image" accept="image/x-png,image/gif,image/jpeg" multiple>
            </div>
            {{-- Other field start --}}

            <div class="form-row mt-3">
                <div class="form-group col-md-3 col-sm-12">
                    <label>{{ __('Full Name') }}</label>
                    <input id="fullname" class="form-control" name="fullname" value="" required>
                </div>
                <div class="form-group col-md-3 col-sm-12">
                    <label>{{ __('Lives At') }}</label>
                    <input id="location" class="form-control" name="live" value="" required>
                </div>
                <div class="form-group col-md-3 col-sm-12">
                    <label>{{ __('Age') }}</label>
                    <input id="age" type="number" class="form-control" name="age" value="" required>
                </div>
                <div class="form-group col-md-3 col-sm-12">
                    <label for="gender">{{ __('Gender') }}</label>
                    <select id="gender" class="form-control form-control-sm" aria-label="Default select example">
                        <option value="2" selected>{{ __('Female') }}</option>
                        <option value="1">{{ __('Male') }}</option>
                    </select>
                </div>
            </div>

            <div class="form-row ">
                <div class="form-group col-md-6 col-sm-12">
                    <label for="about">{{ __('About') }} </label>
                    <textarea id="about" name="about" class="form-control" required></textarea>
                </div>

                <div class="form-group col-md-6 col-sm-12">
                    <label for="bio">{{ __('Bio') }} </label>
                    <textarea id="bio" name="bio" class="form-control" required></textarea>
                </div>
            </div>

            <div class="form-row ">
                <div class="form-group col-md-4 col-sm-12">
                    <label>{{ __('YouTube') }}</label>
                    <input id="youtube" class="form-control" name="youtube" value="">
                </div>
                <div class="form-group col-md-4 col-sm-12">
                    <label>{{ __('Facebook') }}</label>
                    <input id="facebook" class="form-control" name="facebook" value="">
                </div>
                <div class="form-group col-md-4 col-sm-12">
                    <label>{{ __('Instagram') }}</label>
                    <input id="instagram" class="form-control" name="instagram" value="">
                </div>
            </div>

            <div class="form-row ">
                <div class="form-group col-md-3 col-sm-12">
                    <label>{{ __('Password') }}</label>
                    <input id="password" class="form-control" name="password" value="" required>
                </div>
            </div>

            <div class="form-group">
                <input class="btn btn-primary mr-1" type="submit" value=" {{ __('Submit') }}">
            </div>

        </form>

    </div>
</div>
@endsection