<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ImportExcelReturnRowsExport implements FromCollection,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */

    private $_Rows;

    public function __construct( $rows = [] ){
        
        $this->_Rows = $rows;

    }
    public function collection()
    {
        //
        return collect( $this->_Rows );
    }
}
