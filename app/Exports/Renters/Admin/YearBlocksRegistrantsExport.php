<?php

namespace App\Exports\Renters\Admin;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use \App\Models\Renters\Year;

class YearBlocksRegistrantsExport implements FromView, ShouldAutoSize, WithStyles
{
  public function __construct (Year $year) {
    $this->year = $year;
  }

  /**
  * @return \Illuminate\Support\View
  */
  public function view () : View
  {
    return view("renters.admin.partials.year_renter_excel_details", ["year" => $this->year]);
  }

  public function styles (Worksheet $sheet) {
    return [
      1 => ['font' => ['bold' => true]]
    ];
  }
}
