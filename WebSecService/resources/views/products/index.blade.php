@foreach($products as $product)
    <div class="product">
        <h3>{{ $product->name }}</h3>
        <p>{{ $product->price }}</p>
        <form action="{{ route('products.buy', $product->id) }}" method="post">
            {{ csrf_field() }}
            <button type="submit" class="btn btn-primary">Buy</button>
        </form>
    </div>
@endforeach