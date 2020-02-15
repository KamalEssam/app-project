<?php

use Illuminate\Database\Seeder;
use App\Models\Visit;

class VisitSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       /* $faker = \Faker\Factory::create();

        for ($i = 0; $i < 20; $i++) {
            Visit::create([
                'user_id' => $faker->numberBetween($from = 1, $to = 18),
                'clinic_id' => $faker->numberBetween($from = 1, $to = 18),
                'reservation_id' => $faker->numberBetween($from = 1, $to = 18),
                'type' => $faker->numberBetween($from = 0, $to = 1),
                'complaint' => $faker->text($maxNbChars = 200),
                'diagnosis' => $faker->text($maxNbChars = 200),
                'next_visit' => $faker->date($format = 'Y-m-d'),
            ]);
        }*/

        $row = new Visit();//1
        $row->user_id = 6;
        $row->clinic_id = 1;
        $row->reservation_id = 1;
        $row->type = 1;
        $row->diagnosis = 'when an unknown printer took a galley of type and scrambled it to make a type specimen book.';
        $row->next_visit = "2018-03-07";
        $row->created_by = 4;
        $row->updated_by = 4;
        $row->save();


        $row = new Visit();//1
        $row->user_id = 5;
        $row->clinic_id = 2;
        $row->reservation_id = 4;
        $row->type = 1;
        $row->diagnosis = 'when an unknown printer took a galley of type and scrambled it to make a type specimen book.';
        $row->next_visit = "2018-03-11";
        $row->created_by = 4;
        $row->updated_by = 4;
        $row->save();

        $row = new Visit();//1
        $row->user_id = 25;
        $row->clinic_id = 3;
        $row->reservation_id = 6;
        $row->type = 1;
        $row->diagnosis = 'when an unknown printer took a galley of type and scrambled it to make a type specimen book.';
        $row->next_visit = "2018-04-11";
        $row->created_by = 24;
        $row->updated_by = 24;
        $row->save();

        $row = new Visit();//1
        $row->user_id = 25;
        $row->clinic_id = 4;
        $row->reservation_id = 9;
        $row->type = 1;
        $row->diagnosis = 'when an unknown printer took a galley of type and scrambled it to make a type specimen book.';
        $row->next_visit = "2018-06-11";
        $row->created_by = 24;
        $row->updated_by = 24;
        $row->save();

        $row = new Visit();//1
        $row->user_id = 26;
        $row->clinic_id = 5;
        $row->reservation_id = 10;
        $row->type = 1;
        $row->diagnosis = 'when an unknown printer took a galley of type and scrambled it to make a type specimen book.';
        $row->next_visit = "2018-05-11";
        $row->created_by = 24;
        $row->updated_by = 24;
        $row->save();
    }
}
