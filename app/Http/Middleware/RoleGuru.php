<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleGuru
{
    /**
     * Hanya izinkan akses jika role adalah guru atau super_admin
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['guru', 'super_admin'])) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk Guru.');
        }

        return $next($request);
    }
}
