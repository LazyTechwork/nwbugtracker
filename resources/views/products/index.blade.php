@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Все продукты</h1>
                <table class="table">
                    <tbody>
                    @foreach($products as $prod)
                        <tr>
                            <td style="width: 100px;"><img src="{{ $prod->getImage() }}" alt=""
                                                           width="100"></td>
                            <td>
                                <a href="{{ route('products.show', ['id'=>$prod->id]) }}">{{ $prod->name }}</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
