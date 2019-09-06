@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Создание нового обновления для продукта</h1>
                <form action="{{ route('products.newupd', ['id'=>$prod->id]) }}" method="POST">
                    @csrf
                    <img src="{{ $prod->getImage() }}" height="250" alt="">
                    <div class="form-group">
                        <label for="prodname">Продукт</label>
                        <input type="text" class="form-control" id="prodname" value="{{ $prod->name }}" disabled>
                    </div>
                    <div class="form-group">
                        <label for="prodversion">Версия</label>
                        <input type="text" name="version" class="form-control" id="prodversion"
                               value="{{ old('version') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="proddesc">Список изменений</label>
                        <textarea name="changelog" id="proddesc" rows="4" class="form-control"
                                  required>{{ old('changelog') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="prodtime">Московское время выпуска обновления <span class="text-muted">(дата и время должны быть не меньше текущего и не больше одного дня позже, для безопасности на клиентской стороне мы ограничили ввод)</span></label>
                        <input type="datetime-local" name="time" class="form-control" id="prodtime"
                               value="{{ old('time', \Carbon\Carbon::now()->format('Y-m-d\TH:i')) }}"
                               min="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}"
                               max="{{\Carbon\Carbon::now()->addDay()->format('Y-m-d\TH:i')}}" required>
                    </div>
                    <button type="submit" class="btn btn-success">Создать</button>
                </form>
            </div>
        </div>
    </div>
@endsection
