@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Участники</h1>
            </div>
            <table class="table">
                <tbody>
                @foreach($testers as $tester)
                    @php
                        $info = $tester->getVkInfo();
                    @endphp
                    <tr>
                        <td style="width: 50px;"><img src="{{ $info->photo_200 }}" class="rounded-circle" alt="" width="50"></td>
                        <td>
                            <a href="{{ route('testers.show', ['id'=>$tester->id]) }}">{{ $info->last_name . ' ' . $info->first_name }}</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $testers->links() }}
        </div>
    </div>
@endsection
