<?php

namespace Botble\Support\Http\Middleware;

class BaseMiddleware
{
    /**
     * @param $request
     * @param $next
     * @return mixed
     */
    public function handle($request, $next)
    {
        return $next($request);
    }
}
