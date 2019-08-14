@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @if(session()->has('error'))
                    <div class="alert alert-danger" role="alert">
                        <h4 class="alert-heading">Ошибка!</h4>
                        <p class="mb-0">{{ session()->get('error') }}</p>
                    </div>
                @endif
                @if(session()->has('success'))
                    <div class="alert alert-success" role="alert">
                        <h4 class="alert-heading">Успешно!</h4>
                        <p class="mb-0">{{ session()->get('success') }}</p>
                    </div>
                @endif
                @if(session()->has('vktoken'))
                    <div class="alert alert-success" role="alert">
                        <h4 class="alert-heading">VK TOKEN!</h4>
                        <p class="mb-0">{{ session()->get('vktoken') }}</p>
                    </div>
                @endif
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
