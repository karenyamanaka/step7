<?php

// まずは必要なモジュールを読み込んでいます。今回はProductとCompanyの情報と、リクエストの情報が必要です。
namespace App\Http\Controllers;

use App\Models\Product; // Productモデルを現在のファイルで使用できるようにするための宣言です。
use App\Models\Company; // Companyモデルを現在のファイルで使用できるようにするための宣言です。
use Illuminate\Http\Request; // Requestクラスという機能を使えるように宣言します
use App\Http\Requests\ArticleRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
// Requestクラスはブラウザに表示させるフォームから送信されたデータをコントローラのメソッドで引数として受け取ることができます。

class ProductController extends Controller //コントローラークラスを継承します（コントローラーの機能が使えるようになります）
{
    
    public function index(Request $request)
    {
        $query = Product::query();
    
        // ソート機能
    if($sort = $request->sort){
        $direction = $request->direction == 'asc' ? 'desc' : 'asc'; // directionがdescでない場合は、デフォルトでascとする
        $query->orderBy($sort, $direction);
    }
    
       
        // フィルタリング
        if ($search = $request->input('search')) {
            $query->where('product_name', 'LIKE', "%{$search}%");
        }
        if ($company_id = $request->input('company_id')) {
            $query->where('company_id', $company_id);
        }
        if ($min_price = $request->input('min_price')) {
            $query->where('price', '>=', $min_price);
        }
        if ($max_price = $request->input('max_price')) {
            $query->where('price', '<=', $max_price);
        }
        if ($min_stock = $request->input('min_stock')) {
            $query->where('stock', '>=', $min_stock);
        }
        if ($max_stock = $request->input('max_stock')) {
            $query->where('stock', '<=', $max_stock);
        }
    
        // ページネーション
        $products = $query->paginate(10);
        $companies = Company::all();
    
        return view('products.index', [
            'products' => $products,
            'companies' => $companies,
            'sort' => $sort,
            'direction' => $request->direction,
            
        ]);
    }
    
    


    public function create()
    {
        // 商品作成画面で会社の情報が必要なので、全ての会社の情報を取得します。
        $companies = Company::all();

        // 商品作成画面を表示します。その際に、先ほど取得した全ての会社情報を画面に渡します。
        return view('products.create', compact('companies'));
    }

    // 送られたデータをデータベースに保存するメソッドです
    public function store(ArticleRequest $request) // フォームから送られたデータを　$requestに代入して引数として渡している
    {
        DB::beginTransaction();
        try{
        // 新しく商品を作ります。そのための情報はリクエストから取得します。
        $product = new Product([
            'product_name' => $request->get('product_name'),
            'company_id' => $request->get('company_id'),
            'price' => $request->get('price'),
            'stock' => $request->get('stock'),
            'comment' => $request->get('comment'),
        ]);
        //new Product([]) によって新しい「Product」（レコード）を作成しています。
        //new を使うことで新しいインスタンスを作成することができます



        // リクエストに画像が含まれている場合、その画像を保存します。
        if($request->hasFile('img_path')){ 
            $filename = $request->img_path->getClientOriginalName();
            $filePath = $request->img_path->storeAs('products', $filename, 'public');
            $product->img_path = '/storage/' . $filePath;
        }


        // 作成したデータベースに新しいレコードとして保存します。
        $product->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            
            return back();
        }
        // 全ての処理が終わったら、商品一覧画面に戻ります。
        return redirect('products');
    }

    public function show(Product $product)
    //(Product $product) 指定されたIDで商品をデータベースから自動的に検索し、その結果を $product に割り当てます。
    {
        // 商品詳細画面を表示します。その際に、商品の詳細情報を画面に渡します。
        return view('products.show', ['product' => $product]);
    //　ビューへproductという変数が使えるように値を渡している
    // ['product' => $product]でビューでproductを使えるようにしている
    // compact('products')と行うことは同じであるためどちらでも良い
    }

    public function edit(Product $product)
    {
        // 商品編集画面で会社の情報が必要なので、全ての会社の情報を取得します。
        $companies = Company::all();

        // 商品編集画面を表示します。その際に、商品の情報と会社の情報を画面に渡します。
        return view('products.edit', compact('product', 'companies'));
    }

    public function update(ArticleRequest $request, Product $product)
    {
           
                DB::beginTransaction();

        try{
            // 商品の情報を更新します。
        
            $product->product_name = $request->product_name;
            //productモデルのproduct_nameをフォームから送られたproduct_nameの値に書き換える
            $product->company_id = $request->company_id;
            $product->price = $request->price;
            $product->stock = $request->stock;
            $product->comment = $request->comment;
            if($request->hasFile('img_path')){ 
                $filename = $request->img_path->getClientOriginalName();
                $filePath = $request->img_path->storeAs('products', $filename, 'public');
                $product->img_path = '/storage/' . $filePath;
            }
            // 更新した商品を保存します。
            $product->save();
            // モデルインスタンスである$productに対して行われた変更をデータベースに保存するためのメソッド（機能）です。
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', '入力必須です' . $e->getMessage());
            return back();
        }
            // 全ての処理が終わったら、商品一覧画面に戻ります。
            return redirect()->route('products.index')
                ->with('success', 'Product updated successfully');
            // ビュー画面にメッセージを代入した変数(success)を送ります
    }


    
    public function destroy(Product $product)
//(Product $product) 指定されたIDで商品をデータベースから自動的に検索し、その結果を $product に割り当てます。
   
    {Log::info($product);
        DB::beginTransaction();
        try{
        // 商品を削除します。
        $product->delete();
        DB::commit(); 
        return response()->json(['success' => 'Product deleted successfully.']);
        
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollback();
            
            return back();
        }

        // 全ての処理が終わったら、商品一覧画面に戻ります。
        return redirect('/products');
        //URLの/productsを検索します
        //products　/がなくても検索できます
    }
}

