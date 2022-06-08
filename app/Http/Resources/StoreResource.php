<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $images= json_decode($this->store_image);
        foreach($images as $image)
        {
            $loop[] = url('') . $image;
        }

        return [
            'id' => $this->id,
            'store_name' => $this->store_name,
            'address'=> $this->address,
            'city' => $this->city,
            'privince' => $this->province,
            'store_image' => $loop,
            'owner' => UserResource::collection($this->whenLoaded(relationship: 'user'))
        ];
    }
}
