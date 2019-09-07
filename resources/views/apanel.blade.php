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
                <table class="table">
                    <tbody>
                    @foreach($tstrs as $tester)
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
                                    <p class="mb-0">Будет начислено баллов:
                                        <strong>{{ $points[$tester->user_id] }}</strong></p>
                                    <hr>
                                    <p class="mb-0">За отчёты:</p>
                                    @foreach($tstrbugs[$tester->user_id] as $bug)
                                        <p class="mb-0"><a
                                                href="{{ route('bugs.show', ['id'=> $bug->id]) }}">{{ $bug->name }}</a> ({{
                                                $bug->reward }})</p>
                                    @endforeach
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
                <a href="{{ route('letpts') }}" class="btn btn-danger">Начислить баллы</a>
            </div>
        </div>
    </div>
@endsection
