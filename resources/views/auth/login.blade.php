@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Вход с помощью ВКонтакте</div>

                    <div class="card-body">
                        <a href="{{ $auth->getUrl('') }}" class="btn btn-primary">Войти с помощью <img
                                    src="{{ asset('img/vk_mono.svg') }}" height="30" alt=""></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
