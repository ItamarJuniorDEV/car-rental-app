<?php

namespace App\Http\Controllers;

use OpenApi\Generator;

class SwaggerController extends Controller
{
    public function json()
    {
        $openapi = (new Generator)->generate([app_path()]);

        return response($openapi->toJson(), 200, ['Content-Type' => 'application/json']);
    }

    public function ui()
    {
        return view('swagger', ['jsonUrl' => url('/docs/json')]);
    }
}
