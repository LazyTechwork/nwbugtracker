@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="avatar text-right"><img src="{{ $vkinfo->photo_200 }}" alt="" class="img-fluid rounded"></div>
            </div>
            <div class="col-md-9">
                <h1>Профиль тестировщика</h1>
                <span>Имя: <strong>{{$vkinfo->last_name . ' ' . $vkinfo->first_name}}</strong> <sup>{{$tester->user_id}}</sup></span><br>
                @if (session()->get('isglmod'))
                    <span>Баллы: <strong>{{ $tester->points }}</strong></span><br>
                @endif
                @if(session()->get('isglmod') && $tester->isMod())
                    <span>Модератор: <strong>{{ $tester->moderatorName() }}</strong></span><br>
                @endif
                <span>На должности с <strong>{{ $userdb['data'] }}</strong></span><br>
                @if($tester->kick)
                    <span>Исключён из программы тестирование по причине: "<strong>{{ $tester->reason == 'None' ? '-' : $tester->reason }}</strong>"</span><br>
                    @endif
            </div>
        </div>
    </div>
@endsection
