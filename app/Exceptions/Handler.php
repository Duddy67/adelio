<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\AuthenticationException;

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
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
	});

	/*$this->renderable(function (NotFoundHttpException $e, $request) {
	    // Check for api request.
	    if ($request->is('api/*')) {
		return response()->json([
		    'message' => 'Ressource not found.'
		], 404);
	    }
	});*/
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        // N.B: For API requests ensure the 'Accept' key is present in the request header and 
        //      set to 'application/json' or expectsJson will return false.
	if ($request->expectsJson()) {
	    return response()->json(['error' => 'Unauthenticated.'], 401);
	}

	return redirect()->guest(route('login'));
    }
}
