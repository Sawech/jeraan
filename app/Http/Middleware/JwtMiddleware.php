<?php

namespace App\Http\Middleware;

use App\Helpers\Helper;
use Auth;
use Closure;
use Exception;
use JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
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
            /*if (Auth::user() && is_null(Auth::user()->email_verified_at)) {
                return $this->outApiJson('user_not_verify', trans('main.please_verify_user'));
            }*/

            $user = JWTAuth::parseToken()->authenticate();
            $response = $next($request);

            return $response;
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return $this->outApiJson('token-invalid', trans('main.token_invalid'));
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return $this->outApiJson('token-expired', trans('main.token_expired'));
            } else {
                return $this->outApiJson('token-invalid', trans('main.token_invalid'));
            }
        }
        return $next($request);
    }
}
