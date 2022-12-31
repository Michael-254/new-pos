<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "mobile" => $this->mobile,
            "email" => $this->email,
            "image" => $this->image,
            "state" => $this->state,
            "city" => $this->city,
            "country" => $this->country,
            "zip_code" => $this->zip_code,
            "address" => $this->address,
            "balance" => $this->balance,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "company_id" => $this->company_id,
            "member_id" => $this->member_id,
            "orders_count" => $this->orders_count,
            "loyalty_points" => $this->member->loyalty_points
        ];
    }
}
