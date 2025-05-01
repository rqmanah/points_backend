<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{

    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];


    /**
     * Register the exception handling callbacks for the application.
     */
      public function register(): void
      {
          $this->renderable(function (Throwable $e) {
              //     return response()->json(['status' => "error", 'message' => $e->getMessage()], 404);
              if ($e instanceof QueryException && $e->getCode() == 23000) {
                  return response([
                      'status' => "error",
                      'message' => 'Duplicate Entry'
                  ], Response::HTTP_NOT_FOUND);
              }

              if ($e instanceof NotFoundHttpException) {
                  return response()->json(['status' => "error", 'message' => 'Not Found!'], 404);
              }

              if ($e instanceof AuthenticationException || !$e->getMessage()) {
                  return response()->json([
                      'status' => 'error',
                      'message' => 'Unauthorized',
                  ], 401); // Unauthorized status code
              }
              return response()->json(['status' => "error", 'message' => $e->getMessage()], 404);
          });
      }

}
