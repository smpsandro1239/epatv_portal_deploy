<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Não autenticado.'], 401);
        }

        if (empty($roles) || in_array($request->user()->role, $roles)) {
            return $next($request);
        }

        return response()->json(['message' => 'Acesso não autorizado.'], 403);
    }
}
