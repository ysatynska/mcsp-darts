<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use \App\Models\Game;

class GamesExport implements FromView, ShouldAutoSize, WithStyles
{
  public function __construct ($games) {
    $this->games = $games;
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
}
