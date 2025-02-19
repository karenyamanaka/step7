<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; // Productモデルを使用
use App\Models\Sale; // Saleモデルを使用

class SalesController extends Controller
{
    public function purchase(Request $request)
{
    DB::beginTransaction(); // トランザクションを開始します
    try {
    // リクエストから必要なデータを取得する
    $productId = $request->input('product_id'); // "product_id":7が送られた場合は7が代入される
    $quantity = $request->input('quantity', 1); // 購入する数を代入する もしも”quantity”というデータが送られていない場合は1を代入する

    // データベースから対象の商品を検索・取得
    $product = Product::find($productId); // "product_id":7 送られてきた場合 Product::find(7)の情報が代入される

    // 商品が存在しない、または在庫が不足している場合のバリデーションを行う
    if (!$product) {
        return response()->json(['message' => '商品が存在しません'], 404);
    }
    if ($product->stock < $quantity) {
        return response()->json(['message' => '商品が在庫不足です'], 400);
    }

    // 在庫を減少させる
    $product->stock -= $quantity; // $quantityは購入数を指し、デフォルトで1が指定されている
    $product->save();


    // Salesテーブルに商品IDと購入日時を記録する
    $sale = new Sale([
        'product_id' => $productId,
        // 主キーであるIDと、created_at , updated_atは自動入力されるため不要
    ]);

    $sale->save();

    DB::commit(); // トランザクションをコミットします

            // レスポンスを返す
            return response()->json(['message' => '購入成功']);
        } catch (\Exception $e) {
            DB::rollback(); // エラーが発生した場合、トランザクションをロールバックします
            Log::error($e); // エラーログを記録します

    // レスポンスを返す
    return response()->json(['message' => '購入処理中にエラーが発生しました'], 500);
}

}
}