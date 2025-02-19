<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CheckHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (isset($_SERVER['HTTP_APIKEY'])) {

            $apikey = $_SERVER['HTTP_APIKEY'];

            if ($apikey == 123) {
                return $next($request);
            } else {

                $data['status']    = false;
                $data['message']  = "Enter Right Api key";
                return new JsonResponse($data, 401);
            }
        } else {
            $data['status']    = false;
            $data['message']  = "Unauthorized Access";
            return new JsonResponse($data, 401);
        }
    }
}
