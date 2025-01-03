<?php

use Illuminate\Support\Facades\Route;
// "Route"というツールを使うために必要な部品を取り込んでいます。
use App\Http\Controllers\ProductController;
// ProductControllerに繋げるために取り込んでいます
use Illuminate\Support\Facades\Auth;
// "Auth"という部品を使うために取り込んでいます。この部品はユーザー認証（ログイン）に関する処理を行います



Route::get('/', function () {
    // ウェブサイトのホームページ（'/'のURL）にアクセスした場合のルートです
    if (Auth::check()) {
        // ログイン状態ならば
        return redirect()->route('products.index');
        // 商品一覧ページ（ProductControllerのindexメソッドが処理）へリダイレクトします
    } else {
        // ログイン状態でなければ
        return redirect()->route('login');
        //　ログイン画面へリダイレクトします
    }
});
// もしCompanyControllerだった場合は
// companies.index のように、英語の正しい複数形になります。


Auth::routes();




Route::group(['middleware' => 'auth'], function () {
   
Route::get('/products', [ProductController::class, 'index'])->name('products.index');

//商品登録フォーム
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');

//商品追加
Route::post('/products', [ProductController::class, 'store'])->name('products.store');

//商品詳細
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

//商品更新edit
Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');

//商品更新update
Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');

//商品消去
Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
});






Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
