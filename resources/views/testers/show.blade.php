@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="avatar text-right"><img src="{{ $vkinfo->photo_200 }}" alt="" class="img-fluid rounded">
                </div>
            </div>
            <div class="col-md-9">
                <h1>Профиль тестировщика</h1>
                <span>Имя: <strong>{{$vkinfo->last_name . ' ' . $vkinfo->first_name}}</strong> <sup>{{$tester->user_id}}</sup></span><br>
                @if (session()->get('isglmod'))
                    <span>Баллы: <strong>{{ $tester->points }}</strong></span><br>
                @endif
                <span>Отчёты: <strong>{{ $tester->getBugs->count() }}</strong></span><br>
                @if(session()->get('isglmod') && $tester->isMod())
                    <span>Модератор: <strong>{{ $tester->moderatorName() }}</strong></span><br>
                @endif
                <span>На должности с <strong>{{ $userdb['data'] }}</strong></span><br>
                @if($tester->kick)
                    <span>Исключён из программы тестирования по причине: "<strong>{{ $tester->reason == 'None' ? '-' : $tester->reason }}</strong>"</span>
                    <br>
                @endif
                <h4 class="mt-3">Последние отчёты тестировщика</h4>
                <table class="table">
                    <tbody>
                    @foreach($bugs as $bug)
                        @php
                            $author = $bug->getAuthor;
                            $info = $author->getVkInfo();
                            $prod = $bug->getProduct;
                        @endphp
                        @if(!($bug->getPriority() == 'Уязвимость' && !($prod->isModerator(session()->get('id')) || session()->get('isglmod') || $author->user_id == session()->get('id'))))
                            <tr>
                                <td>
                                    <p class="mb-0"><strong><a
                                                href="{{ route('bugs.show', ['id'=>$bug->id]) }}">{{ $bug->name }}</a></strong>
                                    </p>
                                    @php $uinfo = $bug->getAuthor->getVkInfo(); $prod = $bug->getProduct; @endphp
                                    <p class="mb-0">От <strong><a
                                                href="{{ route('testers.show', ['id'=>$uinfo->user_id]) }}">{{ $uinfo->last_name . ' ' . $uinfo->first_name }}</a></strong>
                                        в продукте <strong><a
                                                href="{{ route('products.show', ['id'=>$prod->id]) }}">{{ $prod->name }}</a></strong>
                                    </p>
                                    <p class="mb-0">Текущий статус <span
                                            class="badge badge-{{ $bug->getStatusColor() }}">{{ $bug->getStatus() }}</span>
                                        &centerdot; Создан {{ $bug->created_at->locale('ru_RU')->diffForHumans() }}</p>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
                <p><a href="{{ route('testers.bugs', ['id'=>$tester->user_id]) }}">Все отчёты тестировщика</a></p>
            </div>
        </div>
    </div>
@endsection
