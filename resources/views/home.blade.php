@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="avatar text-right"><img src="{{ $user['photo_max_orig'] }}" alt="" class="img-fluid rounded"></div>
            </div>
            <div class="col-md-9">
                <h1>Личный кабинет тестировщика</h1>
                <span>Имя: <strong>{{$user['last_name'] . ' ' . $user['first_name']}}</strong> <sup>{{$user['id']}}</sup></span><br>
                <span>Баллы: <strong>{{ $userdb['points'] }}</strong></span><br>
                <span>На должности с <strong>{{ $userdb['data'] }}</strong></span><br>
                @if($userdb['kick'])
                    <span>Исключён из программы тестирование по причине: <strong>{{ $userdb['reason'] == 'None' ? '-' : $userdb['reason'] }}</strong></span><br>
                @endif
            </div>
        </div>
    </div>
@endsection
