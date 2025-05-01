<?php

namespace App\Bll;

use App\Modules\Students\Models\Students;
use App\Modules\Students\Models\StudentsBehaviors;




class StudentStats
{
    // This function will return the total points earned by a student
    /**
     * @param $student_id
     * @return int
     */
    public static function totalpointsEarned($student_id)
    {
        $totalScore = 0;
        $student = Students::find($student_id);
        if ($student) {
            $totalScore = StudentsBehaviors::where('student_id', $student_id)->where('points', '>', 0)->whereNull('prize_id')->sum('points');
        }
        return $totalScore;
    }
    // This function will return the total points deducted from a student
    /**
     * @param $student_id
     * @return int
     */
    public static function totalDeductedpoints($student_id)
    {
        $totalScore = 0;
        $student = Students::find($student_id);
        if ($student) {
            $totalScore = StudentsBehaviors::where('student_id', $student_id)->where('points', '<', 0)->whereNull('prize_id')->sum('points');
        }
        return $totalScore;
    }



    // This function will return the total points a student has spent on a prize
    /**
     * @param $student_id
     * @return int
     */
    public static function totalpointsSpent($student_id)
    {
        $totalScore = 0;
        $student = Students::find($student_id);
        if ($student) {
            $totalScore = StudentsBehaviors::where('student_id', $student_id)
            ->whereNull('behavior_id')
            ->whereNotNull('prize_id')
            ->sum('points');
        }
        return $totalScore;
    }

    // This function will return the actual points a student has
    /**
     * @param $student_id
     * @return int
     */
    public static function actualpoints($student_id)
    {
        $totalScore = 0;
        $student = Students::find($student_id);
        if ($student) {
            $totalScore = StudentsBehaviors::where('student_id', $student_id)->sum('points');
        }
        return $totalScore;
    }

    // This function will return the total prizes a student has redeemed
    /**
     * @param $student_id
     * @return int
     */
    public static function totalPrizesRedeemed($student_id)
    {
        $totalPrizes = 0;
        $student = Students::find($student_id);
        if ($student) {
            $totalPrizes = StudentsBehaviors::where('student_id', $student_id)->whereNotNull('prize_id')->count();
        }
        return $totalPrizes;
    }
}
