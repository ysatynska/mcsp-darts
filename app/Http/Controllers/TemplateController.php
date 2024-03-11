<?php

namespace App\Http\Controllers;

use App\Http\Controllers\GamesController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RanksController;
use App\Models\User;
use RCAuth;

class TemplateController extends Controller
{
    public static $TIME_FORMAT = 'g:i A';

    public function __construct()
    {
        $side_navigation = [
            '<span class="far fa-home"></span> Submit Score' => action([GamesController::class, 'submitScore']),
        ];

        if (RCAuth::check() || RCAuth::attempt()) {
            $side_navigation['<span class="fa-regular fa-ranking-star"></span> Ranks'] = action([RanksController::class, 'showRanks'], ['students_only' => 'yes']);
            $side_navigation['<span class="fa-regular fa-table-tennis-paddle-ball"></span> My Games'] = action([GamesController::class, 'myGames']);

            $rcid = RCAuth::user()->rcid;
            $admin_users = explode(", ", env("ADMIN_USERS"));

            if (in_array($rcid, $admin_users)) {
                $side_navigation['<span class="far fa-list"></span> All Games'] = action([AdminController::class, 'allGames'], ['students_only' => 'yes']);
            }
            $side_navigation['<span class="far fa-sign-out"></span> Logout '.RCAuth::user()->username] = route('logout');
        } else {
            $side_navigation['<span class="far fa-sign-in"></span> Login'] = route('login');
        }
        $this->side_navigation = $side_navigation;
        view()->share('side_navigation', $side_navigation);
    }
}
