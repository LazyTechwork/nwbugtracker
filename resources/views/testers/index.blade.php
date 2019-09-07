@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Участники</h1>
                <form action="{{ route('testers.index') }}" method="GET" class="form-inline">
                    <div class="input-group mb-3 w-100">
                        <input type="text" class="form-control" name="s" value="{{ e(request('s')) }}" placeholder="Поиск тестировщика" aria-label="Поиск тестировщика" aria-describedby="searchbtn">
                        <div class="input-group-append">
                            <button class="btn btn-outline-success" type="submit" id="searchbtn">Найти</button>
                        </div>
                    </div>
                </form>
                <hr>
                <p class="text-muted">Количество тестировщиков: {{ $testers->total() }}</p>
                {{ $testers->links() }}
                <table class="table">
                    <tbody>
                    @foreach($testers as $tester)
                        @php
                            $info = $tester->getVkInfo();
                        @endphp
                        <tr>
                            <td style="width: 50px;">@if($info != null)<img src="{{ $info->photo_200 }}"
                                                                            class="rounded-circle" alt=""
                                                                            width="50">@endif</td>
                            <td class="align-middle">
                                @if($info != null)
                                    <a href="{{ route('testers.show', ['id'=>$tester->user_id]) }}">{{ $info->last_name . ' ' . $info->first_name }}</a>
                                    <p class="mb-0">Отчётов: <strong>{{ $tester->getBugs->count() }}</strong></p>
                                    @if($tester->kick) <p class="text-muted mb-0">Исключён из программы
                                        тестирования</p> @endif
                                @else
                                    <p class="mb-0 text-muted">Не найдено информации в базе данных по тестировщику с
                                        ID {{ $tester->user_id }}</p>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{ $testers->links() }}
            </div>
        </div>
    </div>
@endsection
