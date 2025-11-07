<div id="product-table-wrapper">
    <table id="product-table" class="table border table-striped text-center align-middle">
        <thead class="table-light">
            <tr>
                <th>
                    <a href="#" class="sort-link" data-sort="id">ID</a>
                </th>
                <th>商品画像</th>
                <th>
                    <a href="#" class="sort-link" data-sort="product_name">商品名</a>
                </th>
                <th>
                    <a href="#" class="sort-link" data-sort="price">価格</a>
                </th>
                <th>
                    <a href="#" class="sort-link" data-sort="stock">在庫数</a>
                </th>
                <th>メーカー名</th>
                <th>
                    <a href="{{ route('products.create') }}" class="btn btn-warning btn-sm">新規登録</a>
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
            <tr id="row-{{ $product->id }}">
                <td>{{ $product->id }}</td>
                <td>
                    @if($product->img_path)
                    <img src="{{ asset('storage/' . $product->img_path) }}" width="50">
                    @else
                    画像なし
                    @endif
                </td>
                <td>{{ $product->product_name }}</td>
                <td>¥{{ number_format($product->price) }}</td>
                <td>{{ $product->stock }}</td>
                <td>{{ $product->company->company_name }}</td>
                <td>
                    <a href="{{ route('products.show', $product->id) }}" class="btn btn-info btn-sm">詳細</a>
                    <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="{{ $product->id }}">
                        削除
                    </button>
                </td>

            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-muted text-center">該当する商品がありません。</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center mt-4">
        {{ $products->links('pagination::bootstrap-4') }}
    </div>
</div>