@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Участники</h1>
                <table class="table">
                    <tbody>
                    @foreach($testers as $tester)
                        @php
                            $info = $tester->getVkInfo();
                        @endphp
                        <tr>
                            <td style="width: 50px;"><img src="{{ $info->photo_200 }}" class="rounded-circle" alt=""
                                                          width="50"></td>
                            <td class="align-middle">
                                <a href="{{ route('testers.show', ['id'=>$tester->user_id]) }}">{{ $info->last_name . ' ' . $info->first_name }}</a>
                                <p class="mb-0">Отчётов: {{ $tester->getBugs->count() }}</p>
                                @if($tester->kick) <p class="text-muted mb-0">Исключён из программы тестирования</p> @endif
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
