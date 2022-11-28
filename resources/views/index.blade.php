<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Train</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="//cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4/dark.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/css/app.css?ver='.verTime()) }}">
</head>
<body>
<div class="overload hidden">
    <div class="loading">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
</div>
<div class="container-custome">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/" class="text-dark">新車・中古バイク検索サイト モトサーチ ]> カテゴリ</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $category['name'] }}</li>
        </ol>
    </nav>
    <h1 id="title">{{ $category['name'] }} {{ $totalCar['total_model'] }}車種({{ number_format($totalCar['total_bike']) }}台)</h1>
    <div class="filter">
        <p class="mb-1 mt-2">Filter</p>
        <div class="group-input d-flex align-items-center" data-model="model_kana_prefix">
            <h5 class="title" >頭文字:</h5>
            <div class="list-filter d-flex">
                @foreach($categoryMotoChar as $character)
                    @if(json_decode($character)->type === '1')
                        <a href="{{ pushParamUrl('model_kana_prefix', json_decode($character)->code) }}" class="action-filter
                                @if(!in_array(json_decode($character)->code ,$enable['enableKanaPrefix'])) disable @endif
                                @if(in_array(json_decode($character)->code, $active['activeKanaPrefix'])) active @endif"
                           style="cursor: pointer" >{{ json_decode($character)->name }}</a>
                    @endif
                @endforeach
            </div>
        </div>
        <div class="group-input d-flex align-items-center" data-model="model_name_prefix">
            <h5 class="title" >画面設計:</h5>
            <div class="list-filter d-flex">
                @foreach($categoryMotoChar as $character)
                    @if(json_decode($character)->type === '2')
                        <a href="{{ pushParamUrl('model_name_prefix', json_decode($character)->code) }}" class="action-filter
                                @if(!in_array(json_decode($character)->code ,$enable['enableNamePrefix'])) disable @endif
                                @if(in_array(json_decode($character)->code, $active['activeNamePrefix'])) active @endif"
                           style="cursor: pointer" >{{ json_decode($character)->name }}</a>
                    @endif
                @endforeach
            </div>
        </div>
        <div class="group-input d-flex align-items-center" data-model="model_displacement">
            <h5 class="title" >排気:</h5>
            <div class="list-filter d-flex">
                @foreach($categoryMotoDisplacement as $displacement)
                    <a href="{{ pushParamUrl('model_displacement', json_decode($displacement)->key) }}" class="action-filter
                            @if(!get_value_between_in_array($enable['enableDisplacement'], json_decode($displacement)->from, json_decode($displacement)->to)) disable @endif
                            @if(in_array(json_decode($displacement)->key, $active['activeDisplacement'])) active @endif"
                       style="cursor: pointer"  >{{ json_decode($displacement)->name }}</a>
                @endforeach
            </div>
        </div>
        <div class="group-input d-flex align-items-center" data-model="model_maker_code">
            <h5 class="title" >排気:</h5>
            <div class="list-filter d-flex">
                @foreach($listMaker as $maker)
                    <a href="{{ pushParamUrl('model_maker_code', $maker->model_maker_code) }}" class="action-filter
                            @if(!in_array($maker->model_maker_code ,$enable['enableMakerCode'])) disable @endif
                             @if(in_array($maker->model_maker_code, $active['activeMakerCode'])) active @endif"
                       style="cursor: pointer" >{{ $maker->model_maker_hyouji }}</a>
                @endforeach
            </div>
        </div>
        <a href="{{ url()->current().'?'.http_build_query(request()->only('ctype', 'btype')) }}" class="reset-all d-block text-white">Reset</a>
    </div>
    <div id="cars" class="mt-5" data-category="{{ $category['colmn'] }}">
        <h3 class="mb-2">カテゴリ車種一覧</h3>
        <div class="list-card">
            @forelse($products as $product)
                <div class="card-item d-flex">
                    <a href="" class="d-block"><img src="{{ $product->model_image_url }}" data-src="{{ $product->model_image_url }}" alt="" class="img-fluid" /></a>
                    <div>
                        <div class="card-checkbox d-flex align-items-center">
                            <input type="checkbox" data-code="{{ $product->model_code }}" data-maker="{{ $product->model_maker_code }}" class="checkbox-item" id="product{{ $product->model_code }}">
                            <label for="" class="mb-0"><a href="{{ route('web.area') . '?mmc=' . $product->model_maker_code . '?moc=' . $product->model_code }}">{{ $product->model_name }}</a> ({{ $product->model_count }})</label>
                        </div>
                        <span class="price">{{ ($product->model_kakaku_min / 10000) }} 万円 - {{ ($product->model_kakaku_max / 10000) }} 万円</span>
                    </div>
                </div>
            @empty
            @endforelse
        </div>
        <a href="javascript:;" id="add-car">一度に10車種まで選択できます(10)</a>
    </div>
</div>

</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.6.1.js" integrity="sha256-3zlB5s2uwoUzrXK3BT7AX3FyvojsraNFxCc2vC/7pNI=" crossorigin="anonymous"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="{{ asset('/js/unveil.js') }}"></script>
<script src="{{ asset('/js/app.js?ver='.verTime()) }}"></script>

</html>
