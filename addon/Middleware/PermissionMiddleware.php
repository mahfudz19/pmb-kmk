<?php

namespace Addon\Middleware;

use App\Core\Interfaces\MiddlewareInterface;
use App\Exceptions\AuthorizationException;
use App\Services\SessionService;

class PermissionMiddleware implements MiddlewareInterface
{
    public function __construct(private SessionService $session) {}

    public function handle($request, \Closure $next, array $params = [])
    {
        $requiredPermissions = $params;
        $userRole = $this->session->get('auth.user_role');
        $userPermissions = $this->session->get('auth.user_permissions') ?? [];

        if ($userRole === 'admin') {
            return $next($request);
        }

        $hasAccess = in_array('*', $userPermissions, true);
        if (!$hasAccess) {
            foreach ($requiredPermissions as $permission) {
                if (in_array($permission, $userPermissions, true)) {
                    $hasAccess = true;
                    break;
                }
            }
        }

        if (!$hasAccess) {
            $e = new AuthorizationException("Forbidden. Anda tidak memiliki izin (permission) untuk mengakses halaman ini.");
            $e->hardRedirect();
            throw $e;
        }

        return $next($request);
    }
}
