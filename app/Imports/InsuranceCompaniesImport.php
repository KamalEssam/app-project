<?php

namespace App\Imports;

use App\Http\Repositories\Web\InsuranceCompaniesRepository;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class InsuranceCompaniesImport  implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     * @return bool
     */
    public function model(array $row)
    {
        if (!empty($row['ar_name']) && !empty($row['en_name'])) {
            return (new InsuranceCompaniesRepository())->addInsuranceCompanyFromImport([
                'ar_name' => $row['ar_name'],
                'en_name' => $row['en_name'],
            ]);
        }
    }
}
