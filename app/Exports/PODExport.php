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
use App\Exports\SOUTHExport;
use App\Exports\NORTHExport;

class PODExport implements  WithMultipleSheets, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
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
            'SOUTH' => new SOUTHExport($this->date_endorse,$this->type_date),
            
            // Select by sheet name
            'NORTH' => new NORTHExport($this->date_endorse,$this->type_date)
        ];
    }

   

  
    
    public function title(): string
    {
        return 'SOUTH';
    }
   
}
