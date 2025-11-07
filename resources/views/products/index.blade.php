@extends('layouts.app')

@section('content')
<div class="table-wrapper mx-auto mt-5">
    <h4 class="mb-4">商品一覧画面</h4>

    {{-- 検索フォーム --}}
    <form id="search-form" method="GET" action="{{ route('products.index') }}" class="row mb-4">
        <div class="col-3">
            <input type="text" name="keyword" class="form-control" placeholder="検索キーワード" value="{{ request('keyword') }}">
        </div>
        <div class="col-3">
            <select name="company_id" class="form-control">
                <option value="">メーカー名</option>
                @foreach ($companies as $company)
                <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                    {{ $company->company_name }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-2">
            <input type="number" name="min_price" class="form-control" placeholder="最小価格" value="{{ request('min_price') }}">
        </div>
        <div class="col-2">
            <input type="number" name="max_price" class="form-control" placeholder="最大価格" value="{{ request('max_price') }}">
        </div>
        <div class="col-2">
            <button type="submit" class="btn btn-light btn-sm">検索</button>
        </div>
    </form>

    {{--テーブル部分--}}
    @include('products.table')
</div>
@endsection

{{--スクリプトエリア--}}
@section('scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="base-url" content="{{ url('/') }}">

<script>
    $(function() {
        const baseUrl = $('meta[name="base-url"]').attr('content');
        const token = $('meta[name="csrf-token"]').attr('content');

        //検索フォーム送信時（Ajax）
        $('#search-form').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: $(this).attr('action'),
                type: 'GET',
                data: $(this).serialize(),
                success: function(response) {
                    //テーブル部分を差し替える
                    $('#product-table-wrapper').replaceWith(response);
                },
                error: function() {
                    alert('検索に失敗しました。');
                }
            });
        });

        //削除ボタン処理（Ajax）
        $(document).on('click', '.delete-btn', function() {
            const id = $(this).data('id');

            if (!confirm('削除してもよろしいですか？')) return;

            $.ajax({
                url: baseUrl + '/products/' + id,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: token
                },
                success: function(response) {
                    if (response.success) {
                        $('#row-' + id).fadeOut(500, function() {
                            $(this).remove();
                        });
                    } else {
                        alert('削除に失敗しました: ' + (response.message || '不明なエラー'));
                    }
                },
                error: function(xhr) {
                    alert('削除に失敗しました。ステータス: ' + xhr.status);
                    console.log(xhr.responseText);
                }
            });
        });

        //ソート機能（テーブルヘッダークリック時）
        let currentSort = 'id';
        let currentDirection = 'asc';

        $(document).on('click', '.sort-link', function(e) {
            e.preventDefault();

            const sort = $(this).data('sort');

            //同じ列なら昇降切り替え
            if (currentSort === sort) {
                currentDirection = currentDirection === 'asc' ? 'desc' : 'asc';
            } else {
                currentSort = sort;
                currentDirection = 'asc';
            }

            $.ajax({
                url: $('#search-form').attr('action'),
                type: 'GET',
                data: $('#search-form').serialize() + '&sort=' + currentSort + '&direction=' + currentDirection,
                success: function(response) {
                    $('#product-table-wrapper').replaceWith(response);
                },
                error: function() {
                    alert('並び替えに失敗しました。');
                }
            });
        });

        //購入ボタン処理
        $(document).on('click', '.purchase-btn', function() {
            const productId = $(this).data('id');

            if (!confirm('この商品を購入しますか？')) return;

            $.ajax({
                url: '/api/purchase',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    product_id: productId
                }),
                success: function(response) {
                    if (response.success) {
                        alert(response.message);

                        //対応する行の在庫を1減らす
                        const row = $('#row-' + productId);
                        const stockCell = row.find('td:nth-child(5)');
                        const newStock = parseInt(stockCell.text()) - 1;
                        stockCell.text(newStock);
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    const errorMsg = xhr.responseJSON?.message || '購入に失敗しました。';
                    alert(errorMsg);
                }
            });
        });

    });
</script>
@endsection