<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRequest;
use App\Http\Resources\StoreResource;
use App\Http\Resources\UserResource;
use App\Models\Store;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{
    public function all(Request $request, Store $store)
    {
        $id = $request->input('store_id');
        $data = $store->find($id);
        if($id)
        {
            $user = new UserResource(User::find($data->user_id));
            $detail = new StoreResource($data);
            if($data)
                return ResponseFormatter::success([
                    'detail-store' => $detail,
                    'store-owner' => $user
                ],'Detail Store Retrived Successfully');
            else 
                return ResponseFormatter::error(
                    null,
                    'Cannot get detail store data, Data not found !',
                    402
                );
        }

        return ResponseFormatter::success([
            'all-store' => StoreResource::collection(Store::get())
        ],'Show all data store successfully');
    }

    public function store(StoreRequest $request)
    {
        $data = $request->all();
        $sumStore = Store::where('user_id', Auth::user()->id)->count();

        if($sumStore >= 5)
        {
            return ResponseFormatter::error(
                null, 'Toko tidak boleh lebih dari 5'
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
                try 
                {
                    foreach($request->file('store_image') as $file)
                    {
                        $images[] = Storage::url($file->store('assets/store', 'public'));
                    }

                    $data['user_id'] = Auth::user()->id;
                    $data['store_image'] = json_encode($images, JSON_UNESCAPED_SLASHES);

                    Store::create($data);
                    $new_data = Store::where('user_id', Auth::user()->id)->latest()->get();

                    return ResponseFormatter::success([
                        'store-data' => StoreResource::collection($new_data)
                    ], 'Store created Succrssfully');
        
                }
                catch(Exception $error)
                {
                    return ResponseFormatter::error([
                        'store-data' => []
                    ], $error, 500);
                }
            }
        }
    }

    public function update(Request $request, Store $store)
    {
        $data = $request->all();

        if(count($request->store_image) >= 4)
        {
            return ResponseFormatter::error(
                null, 'Foto tidak boleh lebih dari 3'
            );
        }
        else
        {
            $str = $store->find($request->input('id'));
            foreach($request->file('store_image') as $file)
            {
                $save[] = Storage::url($file->store('assets/store', 'public'));
            }
    
            $data['store_image'] = json_encode($save, JSON_UNESCAPED_SLASHES);
            $str->update($data);
        }
    
        $new_store = $store->find($request->input('id'));

        return ResponseFormatter::success([
            'new-store' => new StoreResource($new_store)
        ],'Store updates successfully', 200);
    }
}
