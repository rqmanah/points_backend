<?php

namespace App\Modules\Teachers\Controllers;

use App\Bll\Utility;
use App\Exports\TeachersExport;
use App\Imports\TeachersErrorRowsExport;
use App\Imports\TeachersImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\AdminBaseController;
use App\Modules\Teachers\Requests\StoreRequest;
use App\Modules\Teachers\Requests\UpdateRequest;
use App\Modules\Teachers\Services\TeachersService;
use App\Modules\Teachers\Requests\StoreExcelRequest;
use App\Modules\Teachers\Requests\DeleteTeachersRequest;
use App\Modules\Teachers\Requests\UpdatePasswordRequest;

class adminController extends AdminBaseController
{
    public function __construct()
    {
        $this->service = new TeachersService();
        $this->StoreRequest = new StoreRequest();
        $this->UpdateRequest = new UpdateRequest();
    }

    public function storeExcel(StoreExcelRequest $request)
    {
        if (!Utility::checkTeacherCount()) {
            $this->sendResponse([], __('api.You have reached the maximum number of teachers'));
        }
        try {
            DB::beginTransaction();
            $file = public_path('temp' . DIRECTORY_SEPARATOR . $request->file);
            $importer = new TeachersImport();
            Excel::import($importer, $file);
            // Get the error count after the import
            $errorCount = $importer->getErrorCount();
            $errorRows = $importer->getErrorRows();
            $maxTeachers = $importer->getMaxTeachers();
            unlink($file);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
        if($maxTeachers == 1){
            return $this->sendResponse([], __('api.You have reached the maximum number of teachers'));
        }
        if ($errorCount > 0) {
            $file = Excel::download(new TeachersErrorRowsExport($errorRows), 'error_teachers_rows.xlsx');
            return $file;

        }
        return $this->sendResponse([], __('api.Teachers created successfully'));
    }

    // update password
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $msg = $this->service->updatePassword($request);
        return $this->sendResponse([], $msg);
    }

    public function downloadExcel()
    {
        return Excel::download(new TeachersExport, 'teachers.xlsx');

    }

    public function deleteTeachers(DeleteTeachersRequest $request)
    {
        $this->service->deleteTeachers($request->ids);
        return $this->sendResponse([], __('api.Teachers deleted successfully'));
    }
}
