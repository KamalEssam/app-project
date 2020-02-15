<?php

use Illuminate\Database\Seeder;
use App\Models\MedicationVisit;

class MedicationVisitSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
     /*   $faker = \Faker\Factory::create();

        for($i = 0; $i < 20; $i++) {
            MedicationVisit::create([
                'visit_id' => $faker->numberBetween($from = 1, $to = 19),
                'medication_id' => $faker->numberBetween($from = 1, $to = 5),
            ]);
        }*/

        $row = new MedicationVisit();//1
        $row->visit_id = 1;
        $row->medication_id = 1;
        $row->save();

        $row = new MedicationVisit();//1
        $row->visit_id = 1;
        $row->medication_id = 3;
        $row->save();

        $row = new MedicationVisit();//1
        $row->visit_id = 1;
        $row->medication_id = 4;
        $row->save();

        $row = new MedicationVisit();//1
        $row->visit_id = 1;
        $row->medication_id = 6;
        $row->save();

        $row = new MedicationVisit();//1
        $row->visit_id = 3;
        $row->medication_id = 1;
        $row->save();

        $row = new MedicationVisit();//1
        $row->visit_id = 3;
        $row->medication_id = 5;
        $row->save();

        $row = new MedicationVisit();//1
        $row->visit_id = 4;
        $row->medication_id = 1;
        $row->save();

        $row = new MedicationVisit();//1
        $row->visit_id = 4;
        $row->medication_id = 5;
        $row->save();

        $row = new MedicationVisit();//1
        $row->visit_id = 5;
        $row->medication_id = 3;
        $row->save();

        $row = new MedicationVisit();//1
        $row->visit_id = 5;
        $row->medication_id = 2;
        $row->save();

        $row = new MedicationVisit();//1
        $row->visit_id = 2;
        $row->medication_id = 2;
        $row->save();

        $row = new MedicationVisit();//1
        $row->visit_id = 2;
        $row->medication_id = 3;
        $row->save();
    }
}
