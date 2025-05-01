<?php

namespace App\Modules\Auth\Resources;
use App\Modules\Auth\Resources\Packages\SchoolPackageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            'id'          => $this->id,
            'title'       => isset($this->Data->first()->title) ? $this->Data->first()->title : null,
            'description' => isset($this->Data->first()->description) ? $this->Data->first()->description : null,
            'address'     => isset($this->Data->first()->address) ? $this->Data->first()->address : null,
            'is_active'   => $this->is_active,
            'type'        => $this->type,
            'gender'      => $this->gender,
            'country_id'  => $this->country_id,
            'image'       => $this->image ? asset($this->image) : null,
            'web_image'   => $this->web_image,
            'grades'      => GradesResource::collection($this->grades),
            'packages'    => SchoolPackageResource::make($this->schoolPackage),
            'classes'     => ClassesResource::collection($this->classes()),
        ];
    }
}
