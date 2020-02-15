<?php

namespace App\Exports;

use App\Http\Repositories\Web\saleLeadsRepository;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LeadsExport implements FromCollection, WithHeadings
{

    public function headings(): array
    {
        return [
            'name',
            'mobile',
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return (new saleLeadsRepository())->getLeadsForExporting();
    }
}
