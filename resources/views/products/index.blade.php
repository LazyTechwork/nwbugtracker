@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Все продукты</h1>
                @forelse($products as $prod)
                    <div>
                        <a href="{{ route('products.show', ['id'=>$prod->id]) }}"><h4>{{ $prod->name }}</h4></a>
                    </div>
                @empty
                    <div>
                        <h3 class="text-muted">Продуктов не обнаружено</h3>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
