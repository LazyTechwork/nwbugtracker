<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'NeoWave Bug-tracker')</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <meta name="theme-color" content="#4D59A0">
    <link rel="shortcut icon" href="{{asset('img/nw_icon.png')}}" type="image/png">
</head>
<body class="d-flex flex-column h-100">
<div id="app" class="d-flex flex-column h-100 bg-white">
    <nav class="navbar navbar-expand-md navbar-light fixed-top navbar-laravel bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ route('login') }}">
                <img src="{{ asset('img/nwbugtracker.svg') }}" height="30" alt="">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                @if(session()->has('vktoken'))
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Профиль</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('bugs.index') }}">Отчёты</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}">Продукты</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('testers.index') }}">Участники</a></li>
                    </ul>
            @endif

            <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto">
                    <!-- Authentication Links -->
                    @if(!session()->has('vktoken'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Вход</a>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('bugs.my') }}">Мои отчёты</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('shop.index') }}">Магазин</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('logout') }}">Выход</a></li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
    <main class="py-5 flex-shrink-0 bg-white">
        <div class="container mt-4">
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
                </div>
            </div>
        </div>
        @yield('content')
    </main>
    <footer class="footer mt-auto py-4 bg-light">
        <div class="container-fluid">
            <div class="row text-center justify-content-center">
                <div class="col-md-3">
                    <div class="text-muted">NeoWave Bug-tracker (World Bots Edition)</div>
                    <div class="text-muted">Copyright <a href="//vk.com/tekly">Tekly Technologies</a>
                        &copy; {{ date('Y') }}</div>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('terms') }}" class="text-muted">Правила пользования</a>
                    <div class="text-muted">Версия <b>1.2.9</b></div>
                </div>
                <div class="col-md-2 d-none d-lg-block">
                    <a href="//vk.com/tekly"><img src="{{ asset('img/tekly.svg') }}" alt="" height="60"></a>
                </div>
            </div>
        </div>
    </footer>
</div>
</body>
</html>
