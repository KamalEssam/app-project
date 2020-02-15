<?php

use Illuminate\Database\Seeder;
use App\Models\Medication;

class MedicationSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $row = new Medication();//1
        $row->name = 'Medication1';
        $row->price = '140.50';
        $row->active_ingredient = 'Active ingredient 1';
        $row->dosage = 'dosage 1';
        $row->dosage_form = 'dosage form 1';
        $row->indication = 'indication 1';
        $row->sub_indication = 'Sub indication 1';
        $row->company = 'company 1';
        $row->save();

        $row = new Medication();//1
        $row->name = 'Medication2';
        $row->price = '150.25';
        $row->active_ingredient = 'Active ingredient 2';
        $row->dosage = 'dosage 2';
        $row->dosage_form = 'dosage form 2';
        $row->indication = 'indication 2';
        $row->sub_indication = 'Sub indication 2';
        $row->company = 'company 2';
        $row->save();

        $row = new Medication();//1
        $row->name = 'Medication3';
        $row->price = '170';
        $row->active_ingredient = 'Active ingredient 3';
        $row->dosage = 'dosage 3';
        $row->dosage_form = 'dosage form 3';
        $row->indication = 'indication 3';
        $row->sub_indication = 'Sub indication 3';
        $row->company = 'company 3';
        $row->save();

        $row = new Medication();//1
        $row->name = 'Medication4';
        $row->price = '185';
        $row->active_ingredient = 'Active ingredient 4';
        $row->dosage = 'dosage 4';
        $row->dosage_form = 'dosage form 4';
        $row->indication = 'indication 4';
        $row->sub_indication = 'Sub indication 4';
        $row->company = 'company 1';
        $row->save();

        $row = new Medication();//1
        $row->name = 'Medication test 5';
        $row->price = '210.50';
        $row->active_ingredient = 'Active ingredient 5';
        $row->dosage = 'dosage 5';
        $row->dosage_form = 'dosage form 5';
        $row->indication = 'indication 5';
        $row->sub_indication = 'Sub indication 5';
        $row->company = 'company 5';
        $row->save();

        $row = new Medication();//1
        $row->name = 'Medication test test 6';
        $row->price = '510.50';
        $row->active_ingredient = 'Active ingredient 6';
        $row->dosage = 'dosage 6';
        $row->dosage_form = 'dosage form 6';
        $row->indication = 'indication 6';
        $row->sub_indication = 'Sub indication 6';
        $row->company = 'company 6';
        $row->save();
    }
}