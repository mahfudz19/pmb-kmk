<?php

namespace Addon\Middleware;

use App\Core\Interfaces\MiddlewareInterface;
use App\Exceptions\HttpException;
use App\Services\SessionService;

class CsrfMiddleware implements MiddlewareInterface
{
    public function __construct(private SessionService $session) {}

    public function handle($request, \Closure $next, array $params = [])
    {
        $method = $request->getMethod();
        
        if (in_array($method, ['get', 'head', 'options'], true)) {
            $this->session->getCsrfToken();
            return $next($request);
        }

        $token = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');

        if (!$this->session->validateCsrfToken($token)) {
            throw new HttpException(419, 'CSRF token mismatch. Silakan refresh halaman dan coba lagi.');
        }

        return $next($request);
    }
}
