<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $images = json_decode($this->product_image);
        foreach($images as $image)
        {
            $loop[] = url('') . $image;
        }

        return [
            'store_id' => $this->id,
            'product_name' => $this->product_name,
            'price' => $this->price,
            'product_image'=> $loop
        ];
    }
}
