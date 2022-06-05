<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\StoreItem;
use Illuminate\Http\Request;
use Exception;


class StoreItemController extends Controller
{
    public function create(Request $request)
    {

        $sumProduct = StoreItem::where('store_id', $request->store_id)->count();

        if($sumProduct >= 10)
        {
            return ResponseFormatter::error(
                null, 'Produk di satu Toko tidak boleh lebih dari 10'
            );
        }
        else
        {
            try {
                $request->validate([
                    'product_id' => ['required','integer'],
                    'store_id' => ['required','integer'],
                    'rak_no' => ['required','string'],
                    'stock' => ['required','integer']
                ]);
    
                $product = new StoreItem();
                $product->product_id = $request->product_id;
                $product->store_id = $request->store_id;
                $product->rak_no = $request->rak_no;
                $product->stock = $request->stock;
                $product->save();
    
                return ResponseFormatter::success([
                    'message' => 'Product Created Successfully',
                ]);
    
            }catch(Exception $error)
            {
                return ResponseFormatter::error(
                    null,'Ada yang salah',$error
                );
            }
        };

    }

    public function productList(Request $request)
    {
        $store_id = $request->input('store_id');
        $products = StoreItem::join('products', 'product_id', 'products.id')
                            ->where('store_id', $store_id)
                            ->select('product_name', 'price', 'stock', 'rak_no', 'product_image');

        if($store_id)
        {
            $data = $products->get();
            $store = Store::find($store_id);

            return ResponseFormatter::success([
                'store_name' => $store->store_name,
                'products' => $data
            ], 'Get list product of store successfully');
        }
        else
        {
            return ResponseFormatter::error([
            ], 'Please enter store id');
        }
    }
}
