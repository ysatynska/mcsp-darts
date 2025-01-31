<?php

namespace App\Exports;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Models\User;

class GamesExport implements WithMultipleSheets {
    private $games;
    private $title;

    public function __construct($games, $title) {
        $this->games = $games;
        $this->title = $title;
    }

    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new GamesSheet($this->games, 'Liz Format', true);
        $sheets[] = new GamesSheet($this->games, 'Weselcouch Format', false);

        return $sheets;
    }
}
