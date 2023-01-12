<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
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
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Throwable $exception)
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
    public function render($request, Throwable $exception)
    {

        // This will replace our 404 response with a JSON response.
        if ($exception instanceof ModelNotFoundException && ($request->isJson() || $request->wantsJson())) {
            return response()->json([
                'status_code' => 404, 
                'status_message' => config('constants.HTTP_STATUS_MSG.404'), 
                'message' => ['Resource item Not Found.'],
                'data' => []
            ], 404);
        }

        if ($exception instanceof NotFoundHttpException) {
			if ($request->isJson() || $request->wantsJson()){
				return response()->json([
					'status_code' => 404, 
					'status_message' => config('constants.HTTP_STATUS_MSG.404'), 
					'message' => ['Resource Not Found.'],
					'data' => []
				], 404);
			} else {
				// $authController = app(\App\Http\Controllers\Admin\Auth\AuthController::class);
				// $logout = $authController->logout();
				return redirect()->route('home');
			}
        }

        if ($exception instanceof MethodNotAllowedHttpException && ($request->isJson() || $request->wantsJson())) {
            return response()->json([
                'status_code' => 405, 
                'status_message' => config('constants.HTTP_STATUS_MSG.405'), 
                'message' => ['Method Not Allowed.'],
                'data' => []
            ], 405);
        }

        return parent::render($request, $exception);
    }
}
