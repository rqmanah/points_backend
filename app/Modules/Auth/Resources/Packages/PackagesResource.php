<?php

namespace App\Modules\Auth\Resources\Packages;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackagesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'order' => $this->order,
            'title' => isset($this->Data->first()->title) ? $this->Data->first()->title : '',
            'description' => isset($this->Data->first()->description) ? $this->Data->first()->description : '',
            'short_description' => isset($this->Data->first()->short_description) ? $this->Data->first()->short_description : '',
            'feature_1' => isset($this->Data->first()->feature_1) ? $this->Data->first()->feature_1 : '',
            'feature_2' => isset($this->Data->first()->feature_2) ? $this->Data->first()->feature_2 : '',
            'feature_3' => isset($this->Data->first()->feature_3) ? $this->Data->first()->feature_3 : '',
            'feature_4' => isset($this->Data->first()->feature_4) ? $this->Data->first()->feature_4 : '',
            'price' => $this->Plans->price,
            'after_price' => $this->Plans->after_price,
            'start_at' => $this->Plans->start_at ? substr($this->Plans->start_at, 0, 10) : null,
            'end_at' => $this->Plans->end_at ? substr($this->Plans->end_at, 0, 10) : null,
            'teachers' => $this->Permissions->teachers_count,
            'students' => $this->Permissions->students_count,
            'prizes_count' => $this->Permissions->prizes_count,
            'color' => $this->color,
        ];
    }
}
