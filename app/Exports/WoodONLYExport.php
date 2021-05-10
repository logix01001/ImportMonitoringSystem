<?php

namespace App\Exports;

use App\Container;
use App\bill_of_lading;
use Maatwebsite\Excel\Cell;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use App\Exports\WOODExport;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class WoodONLYExport implements  WithMultipleSheets, WithTitle, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    *
    */
    private $date_endorse;
    private $type_date;

    public function __construct($date,$type_date){
        $this->date_endorse = $date;
        $this->type_date = $type_date;
    }

   
    public function sheets(): array
    {
        

        return [
        
            // Select by sheet index
            'WOODS ONLY' => new WOODExport($this->date_endorse,$this->type_date),
        ];


    }

    public function title(): string
    {
        return 'WOODS ONLY';
    }
}
