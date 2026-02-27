<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use OpenApi\Generator;

class SwaggerController extends Controller
{
    public function json(): Response
    {
        $openapi = (new Generator)->generate([app_path()]);

        return response((string) $openapi?->toJson(), 200, ['Content-Type' => 'application/json']);
    }

    public function ui(): View
    {
        return view('swagger', ['jsonUrl' => url('/docs/json')]);
    }
}
