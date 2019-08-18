@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card w-100">
                    <h4 class="card-header">{{ $prod->name }} @if($prod->locked)(БЛОКИРОВАН)@endif</h4>
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
                                    @if(!$prod->locked)
                                        <a href="#" class="btn btn-success">Создать отчёт</a>
                                    @else
                                        <button class="btn btn-success" type="button" disabled="disabled">Создать
                                            отчёт
                                        </button>
                                    @endif
                                    <a href="#" class="btn btn-primary">Список отчётов</a>
                                    @if(session()->get('isglmod'))<a
                                            href="{{ route('products.modlist', ['id'=>$prod->id]) }}"
                                            class="btn btn-danger">Модераторы</a> @endif
                                    @if($prod->isModerator(session()->get('user_id')) || session()->get('isglmod')) <a
                                            href="{{ route('products.newupdV', ['id'=>$prod->id]) }}"
                                            class="btn btn-outline-primary">Новое
                                        обновление</a>
                                    @endif
                                </div>
                                <h4 class="card-title">Обновления</h4>
                                @forelse($updates as $vers)
                                    @if($vers->time->lte(\Carbon\Carbon::now()) || $prod->isModerator(session()->get('user_id')) || session()->get('isglmod'))
                                        <h5>{{ $vers->version }} <sup
                                                    class="text-muted">{{ date('d.m.Y H:i', strtotime($vers->time)) }}</sup>
                                        </h5>
                                        <p>{!! $vers->changelog !!}</p>
                                        @if(($vers->time->gte(\Carbon\Carbon::now()->addHour()) && $prod->isModerator(session()->get('user_id'))) || session()->get('isglmod'))
                                            <a href="{{ route('products.delupd', ['id'=>$prod->id, 'updateid'=>$vers->id]) }}"
                                               class="btn btn-outline-danger">Удалить</a>
                                        @endif
                                        <hr>
                                    @endif
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
