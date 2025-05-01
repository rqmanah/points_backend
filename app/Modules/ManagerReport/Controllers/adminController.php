<?php

namespace App\Modules\ManagerReport\Controllers;
use App\Http\Controllers\Controller;
use App\Modules\Students\Models\Students;
use App\Modules\Teachers\Models\Teachers;
use App\Modules\ManagerReport\Models\StudentOrders;
use App\Modules\Students\Models\StudentsBehaviors;

class adminController extends Controller
{

    public function report() {

        // get students count
        $students_count         = Students::all()->count();
        // teacher count
        $teachers_count         = Teachers::all()->count();
        // count of Winning prizes
        $winning_prizes_count   = StudentOrders::where('status' , 'compeletd')->count();
        // count of Winning points
        $winning_points_count   = StudentsBehaviors::where('points', '>', 0)->sum('points');
        // count of Losing points
        $losing_points_count    = StudentsBehaviors::where('points', '<', 0)->whereNull('order_id')->sum('points');
        // count of points
        $points_count           = StudentsBehaviors::sum('points');
        // count of points spent on prizes
        $points_spent_on_prizes = StudentsBehaviors::whereNotNull('order_id')->whereHas('order', function($query) {
            $query->where('status', 'compeletd');
        })->sum('points');

        return $this->sendResponse([
            'students_count'         => $students_count,
            'teachers_count'         => $teachers_count,
            'winning_prizes_count'   => $winning_prizes_count,
            'winning_points_count'   => $winning_points_count,
            'losing_points_count'    => abs($losing_points_count),
            'points_count'           => $points_count,
            'points_spent_on_prizes' => abs( $points_spent_on_prizes),
        ], __('api.Report'));
    }

}
