<?php

namespace App\Modules\Settings\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SettingsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $images = $this->image ? collect(json_decode($this->image, true)) // Use true for associative array
        ->map(fn($img) => env('CUSTOM_URL') ."/". $img) // Transform each image URL to use asset()
        ->toArray() // Convert the collection to an array
        : [];

        return [
            'images' => $images,
            'stock_image' => $this->image ?? null,
            'about_ar' => $this->about_ar,
            'about_en' => $this->about_en,
            'phone' => $this->phone,
            'address_ar' => $this->address_ar,
            'address_en' => $this->address_en,
            'owner_ar' => $this->owner_ar,
            'owner_en' => $this->owner_en,
        ];
    }
}
