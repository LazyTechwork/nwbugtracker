@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <h2>Панель модератора</h2>
                <p><strong>{{ $moderator->moderatorName() }}</strong>, добро пожаловать в панель модератора платформы &laquo;NeoWave Bug-tracker&raquo;</p>
                <h3>Администрируемые продукты <span class="badge badge-secondary">{{ $products->count() }}</span></h3>
                <table class="table">
                    <tbody>
                    @foreach($products as $prod)
                        @if(!$prod->locked || session()->get('isglmod'))
                            <tr>
                                <td style="width: 100px;"><img src="{{ $prod->getImage() }}" alt=""
                                                               width="100" class="rounded"></td>
                                <td class="align-middle">
                                    <a href="{{ route('products.show', ['id'=>$prod->id]) }}">{{ $prod->name }} @if($prod->locked)
                                            (БЛОКИРОВАН)@endif</a>
                                    <p>{!! $prod->description !!}</p>
                                </td>
                            </tr> @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
