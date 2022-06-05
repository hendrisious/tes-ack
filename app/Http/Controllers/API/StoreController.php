<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Store;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');

        $stores = Store::join('users', 'user_id', 'users.id')
                ->select('stores.id as store_id','store_name','address','city','province', 'store_image', 'name');

        if($id)
        {
            $store = $stores->where('stores.id', $id)->first();
            
            if($store)
                return ResponseFormatter::success([
                    'store_id' => $store->store_id,
                    'store_name' => $store->store_name,
                    'address' => $store->address,
                    'city' => $store->city,
                    'province' => $store->province,
                    'store_owner' => $store->name,
                    'store_images' => json_decode($store->store_image) 
                ],'Detail Store Retrived Successfully');
            else 
                return ResponseFormatter::error(
                    null,
                    'Cannot get detail store data!',
                    404
                );
        }

        $alls = $stores->get();

        return ResponseFormatter::success([
            'stores' => $alls
        ],'Stores Data Retrived Successfully');
    }

    public function store(Request $request)
    {
        $sumStore = Store::where('user_id', Auth::user()->id)->count();
        if($sumStore >= 5)
        {
            return ResponseFormatter::error(
                null, 'Toko tidak boleh lebih dari 3'
            );
        }
        else
        {
            if(count($request->store_image) >= 4)
            {
                return ResponseFormatter::error(
                    null, 'Foto tidak boleh lebih dari 3'
                );
            }
            else{
                try {
                    $request->validate([
                        'store_name' => ['required','string', 'max:100'],
                        'address' => ['required','string', 'max:255'],
                        'city' => ['required','string', 'max:50'],
                        'province' => ['required','string', 'max:50'],
                        'store_image[]' => ['image']
                    ]);

                    foreach($request->file('store_image') as $file)
                    {
                        $save[] = Storage::url($file->store('assets/store', 'public'));
                    }
        
                    $store = new Store();
                    $store->user_id = Auth::user()->id;
                    $store->store_name = $request->store_name;
                    $store->address = $request->address;
                    $store->city = $request->city;
                    $store->province = $request->province;
                    $store->store_image = json_encode($save, JSON_UNESCAPED_SLASHES);
                    $store->save();
        
                    $toko = Store::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->first();
        
                    return ResponseFormatter::success([
                        'name' => $toko->store_name,
                        'address' => $toko->address,
                        'city' => $toko->city,
                        'province' => $toko->province,
                        'store_image' => json_decode($toko->store_image)
                    ], 'Store created Succrssfully');
        
                }catch(Exception $error)
                {
                    return ResponseFormatter::error([
                        'message' => 'Ada yang salah',
                        'error' => $error
                    ]);
                }
            }

        }

    }

    public function update(Request $request, Store $store)
    {
        $request->validate([
            'store_name' => ['required','string', 'max:100'],
            'address' => ['required','string', 'max:255'],
            'city' => ['required','string', 'max:50'],
            'province' => ['required','string', 'max:50'],
            'store_image[]' => ['image']
        ]);

        if(count($request->store_image) >= 4)
        {
            return ResponseFormatter::error(
                null, 'Foto tidak boleh lebih dari 3'
            );
        }
        else
        {
            $str = $store->find($request->input('id'));
            $str->store_name = $request->store_name;
            $str->address = $request->address;
            $str->city = $request->city;
            $str->province = $request->province;
    
            foreach($request->file('store_image') as $file)
            {
                $save[] = Storage::url($file->store('assets/store', 'public'));
            }
    
            $str->store_image = json_encode($save, JSON_UNESCAPED_SLASHES);
            $str->save();
        }
    
        $toko = Store::find($request->input('id'));

        return ResponseFormatter::success([
            'name' => $toko->store_name,
            'address' => $toko->address,
            'city' => $toko->city,
            'province' => $toko->province,
            'store_image' => json_decode($toko->store_image)
        ]);
    }
}
