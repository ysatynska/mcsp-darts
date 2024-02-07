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

            // Childers, Liz, Scotty, Michael, Weselcouch
            if ($rcid == '1285521' || $rcid == '1250537' || $rcid == '0732787' || $rcid == '0248715' || $rcid == '0003213')
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
