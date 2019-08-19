@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card w-100">
                    <h4 class="card-header">{{ $bug->name }}</h4>
                    <div class="row no-gutters">
                        <div class="col-md-3">
                            <img src="{{ $prod->getImage() }}" class="card-img-top img-fluid" alt="">
                        </div>
                        <div class="col-md-9">
                            <div class="card-body">
                                <h5 class="card-title">Информация</h5>
                                <p class="mb-0">От <strong><a
                                                href="{{ route('testers.show', ['id'=>$author->user_id]) }}">{{ $author->last_name . ' ' . $author->first_name }}</a></strong>
                                    в продукте <strong><a
                                                href="{{ route('products.show', ['id'=>$prod->id]) }}">{{ $prod->name }}</a></strong>
                                </p>
                                <p>Текущий статус <span
                                            class="badge badge-{{ $bug->getStatusColor() }}">{{ $bug->getStatus() }}</span>
                                    &centerdot; Создан {{ $bug->created_at->locale('ru_RU')->diffForHumans() }}</p>

                                <h5 class="card-title">Шаги воспроизведения</h5>
                                <p>{!! $bug->steps !!}</p>

                                <h5 class="card-title">Фактический результат</h5>
                                <p>{{ $bug->actually }}</p>

                                <h5 class="card-title">Ожидаемый результат</h5>
                                <p>{{ $bug->expectedly }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
