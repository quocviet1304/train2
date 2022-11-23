@forelse($products as $product)
    <div class="card-item d-flex">
        <img src="{{ asset('images/anh.jpg') }}" alt="" class="img-fluid" />
        <div>
            <div class="card-checkbox d-flex align-items-center">
                <input type="checkbox" value="{{ $product->model_code }}" class="checkbox-item" id="product{{ $product->model_code }}">
                <label for="product{{ $product->model_code }}" class="mb-0"><span>{{ $product->model_name }}</span> ({{ $product->model_count }})</label>
            </div>
            <span class="price">{{ ($product->model_kakaku_min / 10000) }} 万円 - {{ ($product->model_kakaku_max / 10000) }} 万円</span>
        </div>
    </div>
@empty
@endforelse
