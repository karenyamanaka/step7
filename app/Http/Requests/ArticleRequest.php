<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            // 
            'product_name' => 'required', //requiredは必須という意味です
            'company_id' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'comment' => 'nullable', //'nullable'はそのフィールドが未入力でもOKという意味です
            'img_path' => 'nullable|image|max:2048',
        ];
    }


public function attributes()
{
    return [
        'product_name' => '商品名',
        'company_id' => 'メーカー',
        'price' => '価格',
        'stock' => '在庫数',
        'comment' => 'コメント',
        'img_path' => '画像',
        
    ];
}

/**
 * エラーメッセージ
 *
 * @return array
 */
public function messages() {
    return [
        'product_name.required' => ':attributeは必須項目です。',
        'comapany_id.required' => ':attributeは必須項目です。',
        'price.required' => ':attributeは必須項目です。',
        'price.numeric' => ':attributeは数字で入力してください。',
        'stock.required' => ':attributeは必須項目です。',
        'stock.numeric' => ':attributeは数字で入力してください。',
        'img_path.max' => ':2MB以下で指定してください。',
    ];
}
}
