<?php

namespace App\Http\Controllers;
use App\Models\Game;
use \App\Exports\Renters\Admin\YearBlocksRegistrantsExport;
use \App\Models\Renters\Year;
use Illuminate\Http\Request;

class AdminController extends TemplateController
{
    public function __construct(){
        parent::__construct();
    }

    public function allGames () {
        $all_games = Game::paginate()->withQueryString();
        $my_games = false;

        return view('adminoptions/allGames', ['all_games' => $all_games, 'my_games' => $my_games]);
    }

    public function export (Year $year) {
        $year->load("blocks.block_maps.renter.gender_details", "blocks.block_maps.renter.emergency_contacts");
        return \Excel::download(new YearBlocksRegistrantsExport($year), sprintf("%s.xlsx", $year->description));
    }
}
