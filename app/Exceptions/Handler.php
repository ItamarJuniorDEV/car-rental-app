<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\CarNotAvailableException;

class Handler extends ExceptionHandler
{
    protected $dontReport = [];

    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function register()
    {
        $this->renderable(function (ResourceNotFoundException $e, $request) {
            return response()->json(['erro' => $e->getMessage()], 404);
        });

        $this->renderable(function (CarNotAvailableException $e, $request) {
            return response()->json(['erro' => $e->getMessage()], 422);
        });

        $this->reportable(function (Throwable $e) {});
    }
}
