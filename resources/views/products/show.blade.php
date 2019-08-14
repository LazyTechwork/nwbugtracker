@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card w-100">
                    <h4 class="card-header">{{ $prod->name }}</h4>
                    <div class="row no-gutters">
                        <div class="col-md-3">
                            <img src="{{ $prod->getImage() }}" class="card-img-top img-fluid" alt="">
                        </div>
                        <div class="col-md-9">
                            <div class="card-body">
                                <h4 class="card-title">Статистика</h4>
                                <h5 class="card-text">Отчётов <span
                                            class="badge badge-secondary">{{ $bugs->count() }}</span><br>
                                    Открыто <span
                                            class="badge badge-primary">{{ $bugs->where('status', '1')->count() }}</span><br>
                                    В обработке <span
                                            class="badge badge-warning">{{ $bugs->where('status', '2')->count() }}</span><br>
                                    Исправлено <span
                                            class="badge badge-success">{{ $bugs->where('status', '3')->count() }}</span>
                                </h5>
                                <div class="mb-2">
                                    <a href="#" class="btn btn-success">Создать отчёт</a>
                                    <a href="#" class="btn btn-primary">Список отчётов</a>
                                    @if(session()->get('id') == 538327743 || session()->get('id') == 242521347)<a
                                            href="#" class="btn btn-danger">Модераторы</a> @endif
                                </div>
                                <h4 class="card-title">Обновления</h4>
                                @forelse($updates as $vers)
                                    <p class="card-text">
                                        {{ $vers->version }}
                                        <small class="text-muted">{{ date('d.m.Y H:i', strtotime($vers->time)) }}</small>
                                        <br>
                                        Описание: <br>
                                        {{ $vers->changelog }}
                                    </p>
                                @empty
                                    <div>
                                        <p class="text-muted">Обновлений этого продукта не найдено</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
