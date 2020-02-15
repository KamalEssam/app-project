<?php

use Illuminate\Database\Seeder;
use App\Models\WorkingHour;

class WorkingHourSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
     /*   $faker = \Faker\Factory::create();

        for ($i = 0; $i < 20; $i++) {
            WorkingHour::create([
                'time' => $faker->time($format = 'H:i:s'),
                'day' => $faker->numberBetween($from = 0, $to = 5),
                'clinic_id' => $faker->numberBetween($from = 1, $to = 18),

            ]);
        }*/
        // account_id 2 clinic_id 1

        $working_hour = new WorkingHour();
        $working_hour->time = '08:00';
        $working_hour->day = 0;
        $working_hour->clinic_id = 1;
        $working_hour->created_by = 4;
        $working_hour->updated_by = 4;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '10:00';
        $working_hour->day = 0;
        $working_hour->clinic_id = 1;
        $working_hour->created_by = 4;
        $working_hour->updated_by = 4;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '18:00';
        $working_hour->day = 1;
        $working_hour->clinic_id = 1;
        $working_hour->created_by = 4;
        $working_hour->updated_by = 4;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '12:00';
        $working_hour->day = 2;
        $working_hour->clinic_id = 1;
        $working_hour->created_by = 4;
        $working_hour->updated_by = 4;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '13:00';
        $working_hour->day = 2;
        $working_hour->clinic_id = 1;
        $working_hour->created_by = 4;
        $working_hour->updated_by = 4;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '15:00';
        $working_hour->day = 2;
        $working_hour->clinic_id = 1;
        $working_hour->created_by = 4;
        $working_hour->updated_by = 4;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '09:00';
        $working_hour->day = 3;
        $working_hour->clinic_id = 1;
        $working_hour->created_by = 4;
        $working_hour->updated_by = 4;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '19:00';
        $working_hour->day = 4;
        $working_hour->clinic_id = 1;
        $working_hour->created_by = 4;
        $working_hour->updated_by = 4;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '23:00';
        $working_hour->day = 5;
        $working_hour->clinic_id = 1;
        $working_hour->created_by = 4;
        $working_hour->updated_by = 4;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '10:00';
        $working_hour->day = 5;
        $working_hour->clinic_id = 1;
        $working_hour->created_by = 4;
        $working_hour->updated_by = 4;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '17:00';
        $working_hour->day = 5;
        $working_hour->clinic_id = 1;
        $working_hour->created_by = 4;
        $working_hour->updated_by = 4;
        $working_hour->save();

        // account_id 2 clinic_id 2

        $working_hour = new WorkingHour();
        $working_hour->time = '14:00';
        $working_hour->day = 0;
        $working_hour->clinic_id = 2;
        $working_hour->created_by = 4;
        $working_hour->updated_by = 4;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '08:00';
        $working_hour->day = 0;
        $working_hour->clinic_id = 2;
        $working_hour->created_by = 4;
        $working_hour->updated_by = 4;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '09:00';
        $working_hour->day = 1;
        $working_hour->clinic_id = 2;
        $working_hour->created_by = 4;
        $working_hour->updated_by = 4;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '10:00';
        $working_hour->day = 2;
        $working_hour->clinic_id = 2;
        $working_hour->created_by = 4;
        $working_hour->updated_by = 4;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '18:00';
        $working_hour->day = 2;
        $working_hour->clinic_id = 2;
        $working_hour->created_by = 4;
        $working_hour->updated_by = 4;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '13:00';
        $working_hour->day = 2;
        $working_hour->clinic_id = 2;
        $working_hour->created_by = 4;
        $working_hour->updated_by = 4;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '11:00';
        $working_hour->day = 3;
        $working_hour->clinic_id = 2;
        $working_hour->created_by = 4;
        $working_hour->updated_by = 4;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '17:00';
        $working_hour->day = 4;
        $working_hour->clinic_id = 1;
        $working_hour->created_by = 4;
        $working_hour->updated_by = 4;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '22:00';
        $working_hour->day = 5;
        $working_hour->clinic_id = 2;
        $working_hour->created_by = 4;
        $working_hour->updated_by = 4;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '11:00';
        $working_hour->day = 5;
        $working_hour->clinic_id = 2;
        $working_hour->created_by = 4;
        $working_hour->updated_by = 4;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '15:00';
        $working_hour->day = 5;
        $working_hour->clinic_id = 2;
        $working_hour->created_by = 4;
        $working_hour->updated_by = 4;
        $working_hour->save();

        // account_id 24  clinic_id 3

        $working_hour = new WorkingHour();
        $working_hour->time = '14:00';
        $working_hour->day = 0;
        $working_hour->clinic_id = 3;
        $working_hour->created_by = 24;
        $working_hour->updated_by = 24;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '08:00';
        $working_hour->day = 0;
        $working_hour->clinic_id = 3;
        $working_hour->created_by = 24;
        $working_hour->updated_by = 24;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '09:00';
        $working_hour->day = 1;
        $working_hour->clinic_id = 3;
        $working_hour->created_by = 24;
        $working_hour->updated_by = 24;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '10:00';
        $working_hour->day = 2;
        $working_hour->clinic_id = 3;
        $working_hour->created_by = 24;
        $working_hour->updated_by = 24;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '18:00';
        $working_hour->day = 2;
        $working_hour->clinic_id = 3;
        $working_hour->created_by = 24;
        $working_hour->updated_by = 24;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '13:00';
        $working_hour->day = 2;
        $working_hour->clinic_id = 3;
        $working_hour->created_by = 24;
        $working_hour->updated_by = 24;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '11:00';
        $working_hour->day = 3;
        $working_hour->clinic_id = 3;
        $working_hour->created_by = 24;
        $working_hour->updated_by = 24;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '17:00';
        $working_hour->day = 4;
        $working_hour->clinic_id = 3;
        $working_hour->created_by = 24;
        $working_hour->updated_by = 24;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '22:00';
        $working_hour->day = 5;
        $working_hour->clinic_id = 3;
        $working_hour->created_by = 24;
        $working_hour->updated_by = 24;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '11:00';
        $working_hour->day = 5;
        $working_hour->clinic_id = 3;
        $working_hour->created_by = 24;
        $working_hour->updated_by = 24;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '15:00';
        $working_hour->day = 5;
        $working_hour->clinic_id = 3;
        $working_hour->created_by = 24;
        $working_hour->updated_by = 24;
        $working_hour->save();

        // account_id 24  clinic_id 4


        $working_hour = new WorkingHour();
        $working_hour->time = '14:00';
        $working_hour->day = 0;
        $working_hour->clinic_id = 4;
        $working_hour->created_by = 24;
        $working_hour->updated_by = 24;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '08:00';
        $working_hour->day = 0;
        $working_hour->clinic_id = 4;
        $working_hour->created_by = 24;
        $working_hour->updated_by = 24;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '09:00';
        $working_hour->day = 1;
        $working_hour->clinic_id = 4;
        $working_hour->created_by = 24;
        $working_hour->updated_by = 24;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '09:00';
        $working_hour->day = 1;
        $working_hour->clinic_id = 4;
        $working_hour->created_by = 24;
        $working_hour->updated_by = 24;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '18:00';
        $working_hour->day = 2;
        $working_hour->clinic_id = 4;
        $working_hour->created_by = 24;
        $working_hour->updated_by = 24;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '14:00';
        $working_hour->day = 2;
        $working_hour->clinic_id = 4;
        $working_hour->created_by = 24;
        $working_hour->updated_by = 24;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '11:00';
        $working_hour->day = 3;
        $working_hour->clinic_id = 4;
        $working_hour->created_by = 24;
        $working_hour->updated_by = 24;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '17:00';
        $working_hour->day = 4;
        $working_hour->clinic_id = 4;
        $working_hour->created_by = 24;
        $working_hour->updated_by = 24;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '22:00';
        $working_hour->day = 4;
        $working_hour->clinic_id = 4;
        $working_hour->created_by = 24;
        $working_hour->updated_by = 24;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '11:00';
        $working_hour->day = 5;
        $working_hour->clinic_id = 4;
        $working_hour->created_by = 24;
        $working_hour->updated_by = 24;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '15:00';
        $working_hour->day = 5;
        $working_hour->clinic_id = 4;
        $working_hour->created_by = 24;
        $working_hour->updated_by = 24;
        $working_hour->save();


        // account_id 24  clinic_id 5


        $working_hour = new WorkingHour();
        $working_hour->time = '14:00';
        $working_hour->day = 0;
        $working_hour->clinic_id = 5;
        $working_hour->created_by = 24;
        $working_hour->updated_by = 24;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '08:00';
        $working_hour->day = 0;
        $working_hour->clinic_id = 5;
        $working_hour->created_by = 24;
        $working_hour->updated_by = 24;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '09:00';
        $working_hour->day = 1;
        $working_hour->clinic_id = 5;
        $working_hour->created_by = 24;
        $working_hour->updated_by = 24;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '09:00';
        $working_hour->day = 1;
        $working_hour->clinic_id = 5;
        $working_hour->created_by = 24;
        $working_hour->updated_by = 24;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '18:00';
        $working_hour->day = 2;
        $working_hour->clinic_id = 5;
        $working_hour->created_by = 24;
        $working_hour->updated_by = 24;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '15:00';
        $working_hour->day = 2;
        $working_hour->clinic_id = 5;
        $working_hour->created_by = 24;
        $working_hour->updated_by = 24;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '11:00';
        $working_hour->day = 3;
        $working_hour->clinic_id = 5;
        $working_hour->created_by = 24;
        $working_hour->updated_by = 24;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '17:00';
        $working_hour->day = 3;
        $working_hour->clinic_id = 5;
        $working_hour->created_by = 24;
        $working_hour->updated_by = 24;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '22:00';
        $working_hour->day = 4;
        $working_hour->clinic_id = 5;
        $working_hour->created_by = 24;
        $working_hour->updated_by = 24;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '11:00';
        $working_hour->day = 4;
        $working_hour->clinic_id = 5;
        $working_hour->created_by = 24;
        $working_hour->updated_by = 24;
        $working_hour->save();

        $working_hour = new WorkingHour();
        $working_hour->time = '15:00';
        $working_hour->day = 5;
        $working_hour->clinic_id = 5;
        $working_hour->created_by = 24;
        $working_hour->updated_by = 24;
        $working_hour->save();
    }

}
