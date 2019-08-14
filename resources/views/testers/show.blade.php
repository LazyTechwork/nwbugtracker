@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="avatar text-right"><img src="{{ $vkinfo->photo_200 }}" alt="" class="img-fluid"></div>
            </div>
            <div class="col-md-9">
                <h1>Профиль тестировщика</h1>
                <span>Имя: <strong>{{$vkinfo->last_name . ' ' . $vkinfo->first_name}}</strong> <sup>ID{{$tester->user_id}}</sup></span><br>
                <span>На должности с <strong>{{ $userdb['data'] }}</strong></span><br>
            </div>
        </div>
    </div>
@endsection
