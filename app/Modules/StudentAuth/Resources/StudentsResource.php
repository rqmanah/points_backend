<?php

namespace App\Modules\StudentAuth\Resources;

use Illuminate\Http\Request;
use App\Modules\Auth\Resources\RowsResource;
use App\Modules\Auth\Resources\GradesResource;
use App\Modules\Auth\Resources\ClassesResource;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'user_name'     => $this->user_name,
            'guard'         => $this->guard,
            'national_id'   => isset($this->student->national_id) ? $this->student->national_id : null,
            'row' => isset($this->student->row_id) ? new RowsResource($this->student->row) : null,
            'class' => isset($this->student->class_id) ? new ClassesResource($this->student->class) : null,
            'grade' => isset($this->student->grade_id) ? new ClassesResource($this->student->grade) : null,
            'is_active' => $this->is_active,
            'token' => $this->token,
            'image' => $this->image,
            'school' => [
                'id' => $this->school->id,
                'title' => $this->school->Data->first()->title,
                'title'       => isset($this->school->Data->first()->title) ? $this->school->Data->first()->title : null,
                'description' => isset($this->school->Data->first()->description) ? $this->school->Data->first()->description : null,
                'address'     => isset($this->school->Data->first()->address) ? $this->school->Data->first()->address : null,
                'is_active'   => $this->is_active,
                'type'        => $this->type,
                'gender'      => $this->gender,
                'country_id'  => $this->country_id,
                'image'       => $this->image ? asset($this->image) : null,
                'grades'      => GradesResource::collection($this->school->grades),
            ],
            'has_school'  =>  true ,
            'has_package' =>  true


        ];
    }
}
