@forelse($products as $product)
    <div class="card-item d-flex">
        <img src="{{ $product->model_image_url }}" alt="" class="img-fluid lazy" />
        <div>
            <div class="card-checkbox d-flex align-items-center">
                <input type="checkbox" data-code="{{ $product->model_code }}" data-maker="{{ $product->model_maker_code }}" class="checkbox-item" id="product{{ $product->model_code }}">
                <label for="product{{ $product->model_code }}" class="mb-0"><span>{{ $product->model_name }}</span> ({{ $product->model_count }})</label>
            </div>
            <span class="price">{{ ($product->model_kakaku_min / 10000) }} 万円 - {{ ($product->model_kakaku_max / 10000) }} 万円</span>
        </div>
    </div>
@empty
@endforelse
