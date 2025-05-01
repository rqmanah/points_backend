<?php

namespace App\Modules\FileService\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\FileService\Requests\StoreRequest;

class adminController extends Controller
{
    public function store(StoreRequest $request): \Illuminate\Http\JsonResponse
    {
        $file = $request->file('file');
        $destinationPath = public_path('temp');
        if (!file_exists($destinationPath)) {
            if (!mkdir($destinationPath, 0755, true) && !is_dir($destinationPath)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $destinationPath));
            }
        }
        $newFileName = uniqid('', true) . '.' . $file->getClientOriginalExtension();
        $file->move($destinationPath, $newFileName);
        return $this->sendResponse($newFileName, __('api.File uploaded successfully.'));
    }
}
