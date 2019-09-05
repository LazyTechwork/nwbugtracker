@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card w-100">
                    <h4 class="card-header">{{ $bug->name }}</h4>
                    <div class="row no-gutters px-2">
                        <div class="col-md-3">
                            <img src="{{ $prod->getImage() }}" class="card-img-top img-fluid rounded" alt=""><br>
                            @if ($prod->isModerator(session()->get('user_id')) || session()->get('isglmod'))
                                <button href="#" class="btn btn-light w-100" data-toggle="modal"
                                        data-target="#changeStatus">Изменить статус
                                </button>
                                <div class="modal fade" id="changeStatus" tabindex="-1" role="dialog"
                                     aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Изменить статус</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('bugs.updateStatus', ['id'=>$bug->id]) }}"
                                                      method="POST" id="changeStatusForm">
                                                    @csrf
                                                    <div class="form-group">
                                                        <label for="newStatus">Новый статус</label>
                                                        <select class="form-control" name="status" id="newStatus"
                                                                required>
                                                            @foreach(\App\Bug::$statuses as $status)
                                                                @if($bug->status != $loop->index)
                                                                    <option
                                                                        value="{{ $loop->index }}">{{ $status }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="newStatusComment">Комментарий</label>
                                                        <textarea class="form-control" id="newStatusComment"
                                                                  name="comment" rows="3"></textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="rewardField">Вознаграждение</label>
                                                        <input type="number" class="form-control" name="reward"
                                                               id="rewardField" value="0">
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                    Отмена
                                                </button>
                                                <button type="submit" form="changeStatusForm" class="btn btn-primary">
                                                    Изменить
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @elseif ($bug->canBeReopened() && session()->get('id') == $author->user_id)
                                <button href="#" class="btn btn-light w-100" data-toggle="modal"
                                        data-target="#changeStatus">Переоткрыть
                                </button>
                                <div class="modal fade" id="changeStatus" tabindex="-1" role="dialog"
                                     aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Переоткрыть отчёт</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('bugs.updateStatus', ['id'=>$bug->id]) }}"
                                                      method="POST" id="changeStatusForm">
                                                    @csrf
                                                    <div class="form-group">
                                                        <label for="newStatusComment">Комментарий</label>
                                                        <textarea class="form-control" id="newStatusComment"
                                                                  name="comment" rows="3"></textarea>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                    Отмена
                                                </button>
                                                <button type="submit" form="changeStatusForm" class="btn btn-primary">
                                                    Переоткрыть
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <div class="card-body">
                                <h4 class="card-title">Информация</h4>
                                @if(!$bug->isActualVersion() && session()->get('id') == $author->user_id)
                                    <div class="card">
                                        <div class="card-body">
                                            <p>Актуально в версии
                                                <strong>{{ $prod->getLatestVersion()->version }}</strong>? <br>
                                                Опубликовано обновление для <strong>{{ $prod->name }}</strong>.
                                                Пожалуйста,
                                                проверьте, решена ли проблема в новой версии.</p>
                                            <p class="mb-0"><a
                                                    href="{{ route('bugs.actualityChange', ['id'=>$bug->id,'actual'=>1]) }}"
                                                    class="btn btn-primary">Актуально</a> <a
                                                    href="{{ route('bugs.actualityChange', ['id'=>$bug->id,'actual'=>0]) }}"
                                                    class="btn btn-light">Нет,
                                                    закрыть отчёт</a></p>
                                        </div>
                                    </div> <br>
                                @endif
                                <p class="mb-0">От <strong><a
                                            href="{{ route('testers.show', ['id'=>$author->user_id]) }}">{{ $author->last_name . ' ' . $author->first_name }}</a></strong>
                                    в продукте <strong><a
                                            href="{{ route('products.show', ['id'=>$prod->id]) }}">{{ $prod->name }}</a></strong>
                                    с версией <strong>{{ $bug->getProductVersion() }}</strong>
                                </p>
                                <p>Текущий статус <span
                                        class="badge badge-{{ $bug->getStatusColor() }}">{{ $bug->getStatus() }}</span>
                                    &centerdot; Создан {{ $bug->created_at->locale('ru_RU')->diffForHumans() }}</p>

                                <h5 class="card-title">Шаги воспроизведения</h5>
                                <p>{!! $bug->steps !!}</p>

                                <h5 class="card-title">Фактический результат</h5>
                                <p>{{ $bug->actually }}</p>

                                <h5 class="card-title">Ожидаемый результат</h5>
                                <p>{{ $bug->expectedly }}</p>

                                <h4 class="card-title">Обновления</h4>
                                @forelse($updates as $upd)
                                    @php $upda = $upd->getAuthor; $updavk = $upda->getVkInfo(); @endphp
                                    <p>
                                        <strong>@if($upd->hidden){{ 'Модератор #'.$upda->id }}@if($prod->isModerator(session()->get('user_id')) || session()->get('isglmod')) (<a
                                                href="{{ route('testers.show', ['id'=>$upda->user_id]) }}">{{ $updavk->last_name . ' ' . $updavk->first_name }}</a>)@endif @else <a
                                                href="{{ route('testers.show', ['id'=>$upda->user_id]) }}">{{ $updavk->last_name . ' ' . $updavk->first_name }}</a> @endif
                                        </strong> <sup class="text-muted">{{ $upd->time->format('d.m.Y H:i') }}</sup>
                                    </p>
                                    <p class="alert alert-info">Новый статус отчёта -
                                        <strong>{{ \App\Bug::$statuses[$upd->status] }}</strong></p>
                                    <p>
                                        @if($upd->comment){!! $upd->comment !!}<br>@endif

                                    </p>
                                    <hr>
                                @empty
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
