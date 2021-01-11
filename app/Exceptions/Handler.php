<?php

namespace App\Exceptions;

use Exception;
use App\Exceptions\ModelNotDefined;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof AuthorizationException) {

            return response()->json(["errors" => [
                "message" => "You are not authorized to access this resource"
            ]], 403);
        }

        if ($exception instanceof ModelNotFoundException) {
            $modelName = strtolower(class_basename($exception->getModel()));
            return response()->json(["errors" => [
                "message" => "The {$modelName} was not found in the database"
            ]], 404);
        }
        if ($exception instanceof NotFoundHttpException) {
            return response()->json(["errors" => [
                "message" => "The specified URL can't be found"
            ]], 404);
        }
        if ($exception instanceof MethodNotAllowedHttpException) {
            return response()->json(["errors" => [
                "message" => "The specified method for this request is invalid"
            ]], 405);
        }



        if ($exception instanceof ModelNotDefined) {
            return response()->json(["errors" => [
                "message" => "No model defined"
            ]], 500);
        }

        if ($exception instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($exception, $request);
        }

        if ($exception instanceof AuthenticationException) {
            return $this->unauthenticated($request, $exception);
        }


        if ($exception instanceof QueryException) {
            $errorCode = $exception->errorInfo[1];
            if ($errorCode == 1451) {
                return response()->json(["errors" => [
                    "message" => "Can't remove this resource permanently. It is related with any other resoource."
                ]], 409);
            }
        }
        if ($exception instanceof HttpException) {
            return response()->json(["errors" => [
                "message" => $exception->getMessage()
            ]], $exception->getStatusCode());
        }

        if (config('app.debug')) {
            return parent::render($request, $exception);
        }

        return response()->json(["errors" => [
            "message" => 'Unexpected exception, please try later'
        ]], 500);
    }

    /**
     * Create a response object from the given validation exception.
     *
     * @param  \Illuminate\Validation\ValidationException  $e
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        if ($e->response) {
            return $e->response;
        }
        return $this->invalidJson($request, $e);
    }
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }
}
