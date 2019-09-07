@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <h2>Панель управления Баг-трекером</h2>
                <h5>О великий Главный модератор платформы, добро пожаловать!</h5>
                <p>Начисления ждут: <strong>{{ $testersawait }}</strong> тестировщиков в
                    <strong>{{ $bugsawait }}</strong> отчётах с общим количеством баллов
                    <strong>{{ $pointsawait }}</strong></p>
            </div>
        </div>
    </div>
@endsection
