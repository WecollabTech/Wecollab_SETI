<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    // public function share(Request $request): array
    // {
    //     return [
    //         ...parent::share($request),
    //         'auth' => [
    //             'user' => $request->user(),
    //         ],

    //         'flash' => [
    //         'success' => fn () => $request->session()->get('success'),
    //         'error' => fn () => $request->session()->get('error'),
    //         'warning' => fn () => $request->session()->get('warning'),
    //         'info' => fn () => $request->session()->get('info'),
    //     ],
    //     ];
    // }


    // public function share(Request $request): array
    // {
    //     return [
    //         ...parent::share($request),

    //         'auth' => [
    //             'user' => $request->user() ? [
    //                 // 'id' => $request->user()->id,
    //                 'name' => $request->user()->name,
    //                 // 'email' => $request->user()->email, // opcional
    //                 'email' => $request->routeIs('profile.*')
    //                     ? $request->user()->email
    //                     : null,
    //                 // 'role' => $request->user()->role, // si usas roles
    //             ] : null,
    //         ],

    //         'flash' => [
    //             'success' => fn() => $request->session()->get('success'),
    //             'error' => fn() => $request->session()->get('error'),
    //             'warning' => fn() => $request->session()->get('warning'),
    //             'info' => fn() => $request->session()->get('info'),
    //         ],
    //     ];
    // }

    public function share(Request $request): array
    {
        // Usuario autenticado
        $user = $request->user();

        // Solo exponemos el email en rutas profile.*
        $sharedUser = $user ? [
            'name' => $user->name,
            ...($request->routeIs('profile.*') ? ['email' => $user->email] : []),
        ] : null;

        return [
            ...parent::share($request),

            'auth' => [
                'user' => $sharedUser,
            ],

            'flash' => [
                'success' => fn() => $request->session()->get('success'),
                'error' => fn() => $request->session()->get('error'),
                'warning' => fn() => $request->session()->get('warning'),
                'info' => fn() => $request->session()->get('info'),
            ],
        ];
    }






}