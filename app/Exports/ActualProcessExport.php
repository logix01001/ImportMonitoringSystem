<?php

namespace App\Exports;

use App\bill_of_lading;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ActualProcessExport implements FromCollection, WithHeadings,ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        //
        $list_of_BOL = bill_of_lading::select(
            'bl_no',
            'date_endorse',
            'date_approve_ip',
            'e2m',
            'actual_process',
            'remarks_of_docs',
            'assessment_tag',
            'tsad_no')
            ->
        whereNull('date_endorse')->get();

        return $list_of_BOL;
    }
    public function headings(): array
    {
        return [
            'bl_no',
            'Date Endorse',
            'IP',
            'E2M',
            'Actual Process',
            'Remarks of docs',
            'assessment tag',
            'tsad No.',
        ];
    }
}
