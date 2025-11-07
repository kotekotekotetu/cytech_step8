<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        DB::beginTransaction();
        try {
            $product = Product::findOrFail($request->product_id);

            //在庫チェック
            if ($product->stock <= 0) {
                return response()->json(['success' => false, 'message' => '在庫がありません。'], 400);
            }

            //在庫を1減らす
            $product->decrement('stock', 1);

            //販売履歴を登録
            Sale::create([
                'product_id' => $product->id,
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => '購入が完了しました。']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => '購入処理に失敗しました。', 'error' => $e->getMessage()], 500);
        }
    }
}
