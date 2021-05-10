<?php

namespace App\Exports;
use App\Exports\AllRecordExport;
use App\Container;
use App\bill_of_lading;
use Maatwebsite\Excel\Cell;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AllExport implements WithMultipleSheets, WithTitle,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */


    public function sheets(): array
    {
        
        return [
            // Select by sheet index
            'All' => new AllRecordExport(),
        ];

    }

    
    public function title(): string
    {
        return 'All';
    }

    
}
