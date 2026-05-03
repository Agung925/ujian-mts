<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Daftarkan middleware alias untuk proteksi route berdasarkan role
        $middleware->alias([
            'role.superadmin' => \App\Http\Middleware\RoleSuperAdmin::class,
            'role.guru'       => \App\Http\Middleware\RoleGuru::class,
            'role.siswa'      => \App\Http\Middleware\RoleSiswa::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
