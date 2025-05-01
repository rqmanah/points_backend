<?php

namespace App\Modules\Students\Resources;

use App\Modules\Auth\Resources\ClassesResource;
use App\Modules\Auth\Resources\RowsResource;
use Illuminate\Http\Request;
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
            'id' => $this->id,
            'name' => $this->name,
            'user_name' => $this->user_name,
            'national_id' =>$this->national_id,
            'dialing_code' => $this->dialing_code,
            'row' => isset($this->student->row_id) ? new RowsResource($this->student->row) : null,
            'class' => isset($this->student->class_id) ? new ClassesResource($this->student->class) : null,
            'guardian_phone' => isset($this->student->guardian_phone) ? $this->student->guardian_phone : '',
            'grade' => isset($this->student->grade_id) ? new ClassesResource($this->student->grade) : null,
            'is_active' => $this->is_active,
            'points' => $this->student?->sumPoints(),
            'total_points' => $this->student?->totalPoints(),
            'good_behavior_count' => $this->count_good,
            'bad_behavior_count' => $this->count_bad,

        ];
    }
}
