<?php

namespace App\Http\Middleware;

use App\Helpers\Helper;
use Auth;
use Closure;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class AdminMiddleware extends BaseMiddleware
{
    use Helper;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            if (Auth::user()->role->type == 'user') {
                return $this->outApiJson('JWT_Exception', trans('main.not_permission'));
            }
        } catch (Exception $e) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
        return $next($request);
    }
}
