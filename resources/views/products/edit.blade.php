@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Редактирование продукта</h1>
                <h5>Просьба не пользоваться часто редактированием продукта, делайте это только в исключительных
                    ситуациях</h5>
                <p>Если Вы хотите просто заблокировать/разблокировать продукт нажмите на эту кнопку <a href="{{ route('products.blockprod', ['id'=>$prod->id]) }}"
                                                                                                         class="btn btn-danger">Блокировать/разблокировать продукт</a></p>
                <form action="{{ route('products.editprod', ['id'=>$prod->id]) }}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="prodname">Название продукта</label>
                        <input type="text" class="form-control" id="prodname" name="name"
                               value="{{ old('name', $prod->name) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="proddesc">Описание продукта</label>
                        <textarea name="description" id="proddesc" rows="4" class="form-control"
                                  required>{{ old('description', $prod->getNLdescription()) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="prodimage">Аватарка продукта (обязательно квадратная)</label>
                        <input type="file" class="form-control-file" name="img"
                               accept="image/svg+xml,image/jpeg,image/bmp,image/png,image/pjpeg">
                    </div>
                    <div class="form-group">
                        <label for="prodimagetext">Аватарка продукта (если загружена на сервер и не указана в виде
                            файла)</label>
                        <input type="text" class="form-control" placeholder="wb.svg" name="image"
                               value="{{ old('image', $prod->image) }}" id="prodimagetext">
                    </div>
                    <div class="custom-control custom-checkbox mb-3">
                        <input type="checkbox" name="locked" class="custom-control-input" id="locked"
                               @if($prod->locked) checked @endif>
                        <label class="custom-control-label" for="locked">Блокированный продукт?</label>
                    </div>
                    <button type="submit" class="btn btn-success">Отредактировать</button>
                </form>
            </div>
        </div>
    </div>
@endsection
