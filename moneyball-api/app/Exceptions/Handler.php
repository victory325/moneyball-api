<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
     * @param  \Exception $exception
     *
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception               $exception
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        switch (true) {
            case $exception instanceof ModelNotFoundException:
                $message = 'exception.model_not_found';
                $errors = [$exception->getMessage()];
                $code = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
                break;
            case $exception instanceof AuthenticationException:
                $message = 'exception.unauthenticated';
                $errors = ['exception.unauthenticated'];
                $code = JsonResponse::HTTP_UNAUTHORIZED;
                break;
            case $exception instanceof NotFoundHttpException:
                $message = 'exception.not_found';
                $errors = ['exception.not_found'];
                $code = JsonResponse::HTTP_NOT_FOUND;
                break;
            case $exception instanceof ValidationException:
                $message = 'validation.failed';
                $errors = $exception->errors();
                $code = JsonResponse::HTTP_UNPROCESSABLE_ENTITY;
                break;
            case $exception instanceof MethodNotAllowedHttpException:
                $message = 'exception.method_not_allowed';
                $errors = ['exception.method_not_allowed'];
                $code = JsonResponse::HTTP_METHOD_NOT_ALLOWED;
                break;
            case $exception instanceof BadRequestHttpException:
                $message = $exception->getMessage();
                $errors = [$exception->getMessage()];
                $code = JsonResponse::HTTP_METHOD_NOT_ALLOWED;
                break;
            case $exception instanceof AuthorizationException:
                $message = 'exception.forbidden';
                $errors = [$exception->getMessage()];
                $code = JsonResponse::HTTP_FORBIDDEN;
                break;
            default:
                if (!false) {
                    return parent::render($request, $exception);
                }
                $message = 'exception.internal';
                $errors = ['exception.internal'];
                $code = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
        }
        return response()->json(['error' => $errors, 'message' => $message], $code);
    }
}
