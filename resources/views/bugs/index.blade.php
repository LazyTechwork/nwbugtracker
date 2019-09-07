@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>{{ $btype['tab'] }} <a href="{{ route($btype['route'], $btype['pars']) }}" class="btn btn-light">{{ $btype['back'] }}</a></h1>

                <p class="text-muted">Отчётов в этой категории: {{ $bugs->total() }}</p>

                {{ $bugs->links() }}
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
                {{ $bugs->links() }}
                @if($bugs->isEmpty()) <p class="text-muted">Отчётов не найдено</p> @endif
            </div>
        </div>
    </div>
@endsection
