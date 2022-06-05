<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{

    public function all(Request $request)
    {
        $id = $request->input('id');

        $products = Product::join('users', 'user_id', 'users.id')
                ->select('products.id','product_name','price', 'product_image', 'name as product_owner');

        if($id)
        {
            $product = $products->where('products.id', $id)->first();
            
            if($product)
                return ResponseFormatter::success([
                    'produk_id' => $product->id,
                    'produk_name' => $product->product_name,
                    'price' => $product->price,
                    'porduct_images' => json_decode($product->product_image),
                    'created_by' => $product->product_owner,
                ],
                    'Detail Product Retrived Successfully');
            else 
                return ResponseFormatter::error(
                    null,
                    'Cannot get detail product data!',
                    404
                );
        }

        $all = $products->get();

        return ResponseFormatter::success(
            ['products' => $all],
            'Stores Data Retrived Successfully');
    }

    public function store(Request $request)
    {
        if(count($request->product_image) >= 4)
        {
            return ResponseFormatter::error(
                null, 'Foto tidak boleh lebih dari 3'
            );
        }
        else
        {
            try {
                $request->validate([
                    'product_name' => ['required','string', 'max:100'],
                    'price' => ['required','string', 'max:255'],
                    'product_image[]' => ['image']
                ]);

                $product = new Product();
                $product->user_id = Auth::user()->id;
                $product->product_name = $request->product_name;
                $product->price = $request->price;
                $save = array();
                foreach($request->file('product_image') as $file)
                {
                    $save[] = Storage::url($file->store('assets/product', 'public'));
                }

                $product['product_image'] = json_encode($save, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                $product->save();
    
                $produk = Product::where('user_id', Auth::user()->id)
                                ->orderBy('id', 'desc')->first();
    
                return ResponseFormatter::success([
                    'product' => $product->product_name,
                    'price' => $product->price,
                    'image' => json_decode($produk->product_image)
                ],'Product Created Successfully');
    
            }
            catch(Exception $error)
            {
                return ResponseFormatter::error(
                    null,'Ada yang salah',$error
                );
            }
        }
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'product_name' => ['required','string', 'max:100'],
            'price' => ['required','string', 'max:255'],
            'product_image[]' => ['image']
        ]);

        if(count($request->product_image) >= 4)
        {
            return ResponseFormatter::error(
                null, 'Foto tidak boleh lebih dari 3'
            );
        }
        else
        {
            foreach($request->file('product_image') as $file)
            {
                $save[] = Storage::url($file->store('assets/product', 'public'));
            }
    
            $product = Product::find($request->input('id'));
            $product->user_id = Auth::user()->id;
            $product->product_name = $request->product_name;
            $product->price = $request->price;
            $product->product_image = json_encode($save, JSON_UNESCAPED_UNICODE);
            $product->save();
    
            $products = Product::join('users', 'user_id', 'users.id')
            ->select('products.id','product_name','price', 'product_image', 'name as product_owner')
            ->where('products.id', $request->input('id'))->first();
    
            return ResponseFormatter::success([
                'product' => $products->product_name,
                'price' => $products->price,
                'image' => json_decode($products->product_image)
            ],'Product Update Successfully');
        }
    }
}
