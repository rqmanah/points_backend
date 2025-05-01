<?php

namespace App\Modules\StudentReport\Controllers;

use App\Bll\StudentStats;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class adminController extends Controller
{
    public function report(){
        $userId = Auth::guard('sanctum')?->user()?->id;
        $totalPointsEarned   = StudentStats::totalPointsEarned($userId);
        $totalPointsSpent    = StudentStats::totalPointsSpent($userId);
        $totalActualPoints   = StudentStats::actualPoints($userId);
        $totalPrizesRedeemed = StudentStats::totalPrizesRedeemed($userId);
        $totalDeductedPoints = StudentStats::totalDeductedPoints($userId);

        return $this->sendResponse([
            'totalPointsEarned'   => $totalPointsEarned,
            'totalPointsSpent'    => $totalPointsSpent,
            'totalActualPoints'   => $totalActualPoints,
            'totalPrizesRedeemed' => $totalPrizesRedeemed,
            'totalDeductedPoints' => $totalDeductedPoints,
        ], __('Student report'));
    }
}
