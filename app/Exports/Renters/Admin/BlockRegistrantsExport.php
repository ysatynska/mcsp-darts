<?php

namespace App\Exports\Renters\Admin;

use \App\Models\Renters\Block;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BlockRegistrantsExport implements FromView, ShouldAutoSize, WithStyles
{
    public function __construct (Block $block) {
      $this->block = $block;
    }

    /**
    * @return \Illuminate\Support\View
    */
    public function view () : View
    {
      return view("renters.admin.partials.block_renters_excel", ["block" => $this->block]);
    }

    public function styles (Worksheet $sheet) {
      return [
        1 => ['font' => ['bold' => true]]
      ];
    }
}
