@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card w-100">
                    <div class="row no-gutters py-2 px-2">
                        <div class="col-md-3">
                            <img src="{{ $prod->getImage() }}" class="card-img-top img-fluid rounded" alt="">
                        </div>
                        <div class="col-md-9">
                            <div class="card-body">
                                <h4 class="card-title">{{ $prod->name }} @if($prod->locked)(БЛОКИРОВАН)@endif</h4>
                                <h5>О продукте</h5>
                                <p>{!! $prod->description !!}</p>
                                <h5 class="card-text"><span
                                        class="badge badge-secondary mb-1">Отчётов {{ $prodstat[0] }}</span><br>
                                    <span
                                        class="badge badge-primary mb-1">Открыто {{ $prodstat[1] }}</span><br>
                                    <span
                                        class="badge badge-warning mb-1">В обработке {{ $prodstat[2] }}</span><br>
                                    <span
                                        class="badge badge-success mb-1">Исправлено {{ $prodstat[3] }}</span>
                                </h5>
                                <div class="mb-3">
                                    @if(!$prod->locked)
                                        <a href="{{ route('bugs.newbugV', ['productid'=>$prod->id]) }}" class="btn btn-success">Создать отчёт</a>
                                    @else
                                        <button class="btn btn-success" type="button" disabled="disabled">Создать
                                            отчёт
                                        </button>
                                    @endif
                                    <a href="{{ route('products.bugs', ['id'=>$prod->id]) }}" class="btn btn-primary">Список отчётов</a>
                                    @if(session()->get('isglmod'))<a
                                            href="{{ route('products.modlist', ['id'=>$prod->id]) }}"
                                            class="btn btn-danger">Модераторы</a>
                                        <a href="{{ route('products.editprodV', ['id'=>$prod->id]) }}" class="btn btn-warning">Редактировать продукт</a>
                                        @endif
                                    @if($prod->isModerator(session()->get('user_id')) || session()->get('isglmod')) <a
                                            href="{{ route('products.newupdV', ['id'=>$prod->id]) }}"
                                            class="btn btn-outline-primary">Новое
                                        обновление</a>
                                    @endif
                                </div>
                                <h5 class="card-title">Обновления</h5>
                                @forelse($updates as $vers)
                                    @if($vers->time->lte(\Carbon\Carbon::now()) || $prod->isModerator(session()->get('user_id')) || session()->get('isglmod'))
                                        <h6 class="font-weight-bold">{{ $vers->version }} <sup
                                                    class="text-muted">{{ $vers->time->format('d.m.Y H:i') }}</sup>
                                        </h6>
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
