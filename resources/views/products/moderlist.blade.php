@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card w-100">
                    <h4 class="card-header">{{ $prod->name }} @if($prod->locked)(БЛОКИРОВАН)@endif</h4>
                    <div class="row no-gutters">
                        <div class="col-md-3">
                            <a href="{{ route('products.show', ['id'=>$prod->id]) }}"><img src="{{ $prod->getImage() }}"
                                                                                           class="card-img-top img-fluid"
                                                                                           alt=""></a>
                        </div>
                        <div class="col-md-9">
                            <div class="card-body">
                                <h4 class="card-title">Модераторы</h4>
                                <form action="{{ route('products.addmod', ['id'=>$prod->id]) }}" method="POST">
                                    @csrf
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" required
                                               placeholder="ID нового модератора" aria-label="ID нового модератора"
                                               aria-describedby="addbtn" name="modid">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="submit" id="addbtn">Добавить</button>
                                        </div>
                                    </div>
                                </form>
                                <table class="table w-100">
                                    <tbody>
                                    @foreach($moders as $tester)
                                        @php
                                            $info = $tester->getVkInfo();
                                        @endphp
                                        <tr>
                                            <td style="width: 50px;"><img src="{{ $info->photo_200 }}"
                                                                          class="rounded-circle" alt="" width="50"></td>
                                            <td class="align-middle">
                                                <a href="{{ route('testers.show', ['id'=>$tester->user_id]) }}">{{ $info->last_name . ' ' . $info->first_name }}</a>
                                            </td>
                                            <td class="align-middle">
                                                <a href="{{ route('products.delmod', ['id'=>$prod->id, 'modid'=>$tester->user_id]) }}"
                                                   class="btn btn-outline-danger">Разжаловать</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
