<?php

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $row = new Service();
        $row->en_name = 'Dermatopathology';
        $row->ar_name = 'امراض الجلد';
        $row->account_id = 1;
        $row->save();

        $row = new Service();
        $row->en_name = 'Clinical Psychologist';
        $row->ar_name = 'علم النفس العيادي';
        $row->account_id = 2;
        $row->save();
    }
}