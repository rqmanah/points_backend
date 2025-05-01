<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;


class AdminBaseController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    protected $service;
    protected $UpdateRequest;
    protected $StoreRequest;
    protected function sendResponse($result, $message)
    {
        if ($result && is_object($result) && property_exists($result, 'data')) {
            if (!is_array($result->data)) {
                $result->data = [$result->data];
            }
        } elseif ($result && is_array($result) && !isset($result['data'])) {
            $result = ['data' => $result];
        }elseif ($result && !property_exists($result, 'data') && !is_array($result)){
            $result = ['data' => $result];
        }
        $response = [
            'status' => 'success',
            'result' => $result,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }

    protected function sendError($error, $errorMessages = [], $code = 400)
    {
        $response = [
            'status' => 'error',
            'message' => $error,
            'result' => null
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }
        return response()->json($response, $code);
    }
    protected function show($id)
    {

        $msg = $this->service->showData($id);
        return $this->sendResponse($this->service->getData(), $msg);
    }
    protected function edit($id)
    {
        $msg = $this->service->edit($id);
        return $this->sendResponse($this->service->getData(), $msg);
    }
    protected function store(Request $request)
    {
        //  if (is_array($this->StoreRequest->rules()))
        if ($this->StoreRequest){
            $validator = Validator::make($request->all(), $this->StoreRequest->rules());
            if ($validator->fails())
                return $this->sendError('Validation Error.', $validator->errors());
        }
        $this->service->setRequest($request);
        $msg = $this->service->storeData();
        return $this->sendResponse($this->service->getData(), $msg);
    }
    protected function destroy(int $id)
    {
        $this->service->deleteData($id);
        return $this->sendResponse([], 'Deleted successfully.');
    }

    protected function index()
    {
        $this->service->setRequest(request());
        $msg = $this->service->GetAll();
        return $this->sendResponse($this->service->getData(), $msg);
    }

    protected function update(Request $request, int $id)
    {
        if ($this->UpdateRequest){
            $validator = Validator::make($request->all(), $this->UpdateRequest->rules());
            if ($validator->fails())
                return $this->sendError('Validation Error.', $validator->errors());
        }
        $this->service->setRequest($request);
        $msg = $this->service->updateData($id);
        return $this->sendResponse($this->service->getData(), $msg);
    }
}
