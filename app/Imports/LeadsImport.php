<?php

namespace App\Imports;

use App\Http\Repositories\Web\saleLeadsRepository;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LeadsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        if (!empty($row['name']) && !empty($row['mobile'])) {
            return (new saleLeadsRepository())->addLeadsFromImport([
                'name' => $row['name'],
                'mobile' => $row['mobile'],
                'sale_id' => auth()->user()->id
            ]);
        }
    }
}
