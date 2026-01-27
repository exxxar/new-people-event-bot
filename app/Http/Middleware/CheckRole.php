<?php

namespace App\Http\Middleware;

use App\Enums\RoleEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    protected $rolesHierarchy = [
        'user' => 0,
        'admin' => 1,

    ];

    public function handle(Request $request, Closure $next, $role = null)
    {

        $user = $request->botUser ?? null;

        if (is_null($user)) {
            abort(403, 'Нет доступа');
        }

        if (!is_null($user->blocked_at)) {
            abort(403, 'Нет доступа');
        }

        $userRole = $user->role;

        // если роль пользователя выше или равна требуемой
        if ($userRole >= $this->rolesHierarchy[$role]) {
            return $next($request);
        }

        abort(403, 'Недостаточно прав');
    }
}
