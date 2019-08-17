@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Все продукты</h1>
                @if(session()->get('isglmod'))
                    <a href="{{ route('products.newprodV') }}" class="btn btn-outline-primary mb-3">Добавить новый
                        продукт</a>
                @endif
                <table class="table">
                    <tbody>
                    @foreach($products as $prod)
                        @if(!$prod->locked || session()->get('isglmod'))
                            <tr>
                                <td style="width: 100px;"><img src="{{ $prod->getImage() }}" alt=""
                                                               width="100"></td>
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
