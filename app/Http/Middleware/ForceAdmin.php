<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Http\Controllers\GamesController;
use Closure;
use RCAuth;
use Redirect;

class ForceAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $returnRoute = Redirect::to('login')->with('returnURL', $request->fullUrl());

        $rcid = RCAuth::user()->rcid;

        if (!is_null($rcid)){

            $admin_users = explode(", ", env("ADMIN_USERS"));
            if (in_array($rcid, $admin_users))
            {
                $returnRoute = $next($request);

            } else {
                $returnRoute = redirect()->action([GamesController::class, 'submitScore'])
                ->with('error', 'You do not have permissions to access that page.');
            }
        }

        return $returnRoute;
    }
}
