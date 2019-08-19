@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Новый отчёт о баге</h1>
                <form action="{{ route('bugs.newbug', ['productid'=>$prod->id]) }}" method="POST">
                    @csrf
                    <img src="{{ $prod->getImage() }}" height="250" alt="">
                    <div class="form-group">
                        <label for="prodname">Продукт</label>
                        <input type="text" class="form-control" id="prodname" value="{{ $prod->name }}" disabled>
                    </div>
                    <div class="form-group">
                        <label for="prodname">Версия продукта</label>
                        <input type="text" class="form-control" id="prodname"
                               value="{{ $prod->getLatestVersion()->version }}" disabled>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="bugname">Заголовок</label>
                        <input type="text" class="form-control" id="bugname" placeholder="Коротко о баге" name="name" value="{{ old('name') }}"  max="100" required>
                    </div>
                    <div class="form-group">
                        <label for="bugsteps">Шаги воспроизведения</label>
                        <textarea name="steps" id="bugsteps" rows="7" class="form-control" placeholder="Максимально подробно опишите шаги воспроизведения данного бага, чем точнее будет описано воспроизведение - тем быстрее придут баллы за тестирование ;)"
                                  required>{{ old('steps') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="bugactually">Фактический результат</label>
                        <input type="text" class="form-control" id="bugactually" name="actually"
                               value="{{ old('actually') }}" placeholder="В чём заключается результат бага?" max="450" required>
                    </div>
                    <div class="form-group">
                        <label for="bugexpectedly">Ожидаемый результат</label>
                        <input type="text" class="form-control" id="bugexpectedly" name="expectedly"
                               value="{{ old('expectedly') }}" placeholder="Что бы Вы хотели видеть?" max="450" required>
                    </div>
                    <div class="form-group">
                        <label for="bugtype">Тип проблемы</label>
                        <select name="type" id="bugtype" class="form-control">
                            @foreach(App\Bug::$types as $type)
                                <option value="{{ $loop->index }}" @if($loop->index == old('type')) selected @endif>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="bugpriority">Приоритет</label>
                        <select name="priority" id="bugpriority" class="form-control">
                            @foreach(App\Bug::$priorities as $type)
                                <option value="{{ $loop->index }}" @if($loop->index == 1 || $loop->index == old('priority')) selected @endif>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">Создать</button>
                </form>
            </div>
        </div>
    </div>
@endsection
