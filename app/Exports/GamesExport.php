<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class GamesExport implements FromView, WithTitle, ShouldAutoSize, WithStyles
{
    private $games;
    private $title;

    public function __construct ($games, $title) {
        $this->games = $games;
        $this->title = $title;
    }

    /**
     * @return \Illuminate\Support\View
    */
    public function view () : View
    {
        return view("adminOptions.excel_games", ["games" => $this->games]);
    }

    public function styles (Worksheet $sheet) {
        return [
            1 => ['font' => ['bold' => true]]
        ];
    }
    public function title(): string
    {
        return $this->title;
    }
}
