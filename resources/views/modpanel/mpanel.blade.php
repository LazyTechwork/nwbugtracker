@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <h2>Панель модератора</h2>
                <p><strong>{{ $moderator->moderatorName() }}</strong>, добро пожаловать в панель модератора платформы
                    &laquo;NeoWave Bug-tracker&raquo;</p>
                <h3 id="product_btn" style="cursor: pointer;">Администрируемые продукты <span
                        class="badge badge-secondary">{{ $products->count() }}</span></h3>
                <table class="table" id="products">
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
                <script>
                    $('#products').hide();
                    $('#product_btn').click(function () {
                        $('#products').fadeToggle(500);
                    });
                </script>
                <h3 id="open_btn" style="cursor: pointer;">Открытые отчёты <span
                        class="badge badge-secondary">{{ $open->count() }}</span></h3>
                <table class="table" id="open">
                    <tbody>
                    @foreach($open as $bug)
                        @php
                            $author = $bug->getAuthor;
                            $info = $author->getVkInfo();
                            $prod = $bug->getProduct;
                        @endphp
                        @if(!($bug->getPriority() == 'Уязвимость' && !($prod->isModerator(session()->get('id')) || session()->get('isglmod') || $author->user_id == session()->get('id'))))
                            <tr>
                                <td>
                                    <p class="mb-0"><strong><a
                                                href="{{ route('bugs.show', ['id'=>$bug->id]) }}">{{ $bug->name }}</a></strong>
                                    </p>
                                    @php $uinfo = $bug->getAuthor->getVkInfo(); $prod = $bug->getProduct; @endphp
                                    <p class="mb-0">От <strong><a
                                                href="{{ route('testers.show', ['id'=>$uinfo->user_id]) }}">{{ $uinfo->last_name . ' ' . $uinfo->first_name }}</a></strong>
                                        в продукте <strong><a
                                                href="{{ route('products.show', ['id'=>$prod->id]) }}">{{ $prod->name }}</a></strong>
                                    </p>
                                    <p class="mb-0">Текущий статус <span
                                            class="badge badge-{{ $bug->getStatusColor() }}">{{ $bug->getStatus() }}</span>
                                        &centerdot; Создан {{ $bug->created_at->locale('ru_RU')->diffForHumans() }}</p>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
                <script>
                    $('#open').hide();
                    $('#open_btn').click(function () {
                        $('#open').fadeToggle(500);
                    });
                </script>
                <h3 id="inqueue_btn" style="cursor: pointer;">Отложенные отчёты <span
                        class="badge badge-secondary">{{ $inqueue->count() }}</span></h3>
                <table class="table" id="inqueue">
                    <tbody>
                    @foreach($inqueue as $bug)
                        @php
                            $author = $bug->getAuthor;
                            $info = $author->getVkInfo();
                            $prod = $bug->getProduct;
                        @endphp
                        @if(!($bug->getPriority() == 'Уязвимость' && !($prod->isModerator(session()->get('id')) || session()->get('isglmod') || $author->user_id == session()->get('id'))))
                            <tr>
                                <td>
                                    <p class="mb-0"><strong><a
                                                href="{{ route('bugs.show', ['id'=>$bug->id]) }}">{{ $bug->name }}</a></strong>
                                    </p>
                                    @php $uinfo = $bug->getAuthor->getVkInfo(); $prod = $bug->getProduct; @endphp
                                    <p class="mb-0">От <strong><a
                                                href="{{ route('testers.show', ['id'=>$uinfo->user_id]) }}">{{ $uinfo->last_name . ' ' . $uinfo->first_name }}</a></strong>
                                        в продукте <strong><a
                                                href="{{ route('products.show', ['id'=>$prod->id]) }}">{{ $prod->name }}</a></strong>
                                    </p>
                                    <p class="mb-0">Текущий статус <span
                                            class="badge badge-{{ $bug->getStatusColor() }}">{{ $bug->getStatus() }}</span>
                                        &centerdot; Создан {{ $bug->created_at->locale('ru_RU')->diffForHumans() }}</p>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
                <script>
                    $('#inqueue').hide();
                    $('#inqueue_btn').click(function () {
                        $('#inqueue').fadeToggle(500);
                    });
                </script>
                <h3 id="wip_btn" style="cursor: pointer;">Отчёты в работе <span
                        class="badge badge-secondary">{{ $wip->count() }}</span></h3>
                <table class="table" id="wip">
                    <tbody>
                    @foreach($wip as $bug)
                        @php
                            $author = $bug->getAuthor;
                            $info = $author->getVkInfo();
                            $prod = $bug->getProduct;
                        @endphp
                        @if(!($bug->getPriority() == 'Уязвимость' && !($prod->isModerator(session()->get('id')) || session()->get('isglmod') || $author->user_id == session()->get('id'))))
                            <tr>
                                <td>
                                    <p class="mb-0"><strong><a
                                                href="{{ route('bugs.show', ['id'=>$bug->id]) }}">{{ $bug->name }}</a></strong>
                                    </p>
                                    @php $uinfo = $bug->getAuthor->getVkInfo(); $prod = $bug->getProduct; @endphp
                                    <p class="mb-0">От <strong><a
                                                href="{{ route('testers.show', ['id'=>$uinfo->user_id]) }}">{{ $uinfo->last_name . ' ' . $uinfo->first_name }}</a></strong>
                                        в продукте <strong><a
                                                href="{{ route('products.show', ['id'=>$prod->id]) }}">{{ $prod->name }}</a></strong>
                                    </p>
                                    <p class="mb-0">Текущий статус <span
                                            class="badge badge-{{ $bug->getStatusColor() }}">{{ $bug->getStatus() }}</span>
                                        &centerdot; Создан {{ $bug->created_at->locale('ru_RU')->diffForHumans() }}</p>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
                <script>
                    $('#wip').hide();
                    $('#wip_btn').click(function () {
                        $('#wip').fadeToggle(500);
                    });
                </script>
            </div>
        </div>
    </div>
@endsection
