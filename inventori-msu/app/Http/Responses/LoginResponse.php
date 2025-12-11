<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        $role = Auth::user()->role;

        if ($role === 'pengelola') {
            return redirect()->route('pengelola.beranda');
        } elseif ($role === 'pengurus') {
            return redirect()->route('pengurus.dashboard');
        }

        return redirect()->intended(config('fortify.home'));
    }
}
