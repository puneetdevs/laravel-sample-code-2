<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    
    /**
    * @OA\Info(
    *     description="",
    *     version="1.0.0",
    *     title="Swagger Petstore",
    *     termsOfService="http://swagger.io/terms/",
    *     @OA\Contact(
    *         email=""
    *     ),
    *     @OA\License(
    *         name="Apache 2.0",
    *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
    *     )
    * )
    */
     
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
