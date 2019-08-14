@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="avatar text-right"><img src="{{ $user['photo_max_orig'] }}" alt="" class="img-fluid"></div>
            </div>
            <div class="col-md-9">
                <h1>Личный кабинет тестировщика</h1>
                <span>Имя: <strong>{{$user['last_name'] . ' ' . $user['first_name']}}</strong></span><br>
                <span>Баллы: <strong>{{ $userdb['balls'] }}</strong></span><br>
            </div>
        </div>
    </div>
@endsection
