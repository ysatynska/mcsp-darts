<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class GamesSheet implements FromView, WithTitle, ShouldAutoSize, WithStyles
{
    private $games;
    private $title;
    private $format1;

    public function __construct ($games, $title, $format1) {
        $this->games = $games;
        $this->title = $title;
        $this->format1 = $format1;
    }

    /**
     * @return \Illuminate\Support\View
    */
    public function view () : View
    {
        if ($this->format1) {
            return view("adminOptions.excel_games_format1", ["games" => $this->games]);
        } else {
            return view("adminOptions.excel_games_format2", ["games" => $this->games]);
        }
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
