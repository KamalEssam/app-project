<?php

use Illuminate\Database\Seeder;
use App\Models\Reservation;

class ReservationSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*     $faker = \Faker\Factory::create();

             for($i = 0; $i < 20; $i++) {
                 Reservation::create([
                     'user_id' => $faker->numberBetween($from = 1, $to = 18),
                     'working_hour_id' => $faker->numberBetween($from = 1, $to = 18),
                     'clinic_id' => $faker->numberBetween($from = 1, $to = 18),
                     'status' => $faker->numberBetween($from = 0, $to = 4),
                     'type' => $faker->numberBetween($from = 0, $to = 1),
                     'day' => $faker->date($format = 'Y-m-d'),

                 ]);*/

        $reservation = new Reservation();
        $reservation->user_id = 6;
        $reservation->working_hour_id = 1;
        $reservation->clinic_id = 1;
        $reservation->status = 1;
        $reservation->type = 0;
        $reservation->day = "2018-02-03";
        $reservation->complaint = "Lorem Ipsum is simply dummy text of the printing and typesetting industry.";
        $reservation->created_by = 4;
        $reservation->updated_by = 4;
        $reservation->save();

        $reservation = new Reservation();
        $reservation->user_id = 6;
        $reservation->working_hour_id = 3;
        $reservation->clinic_id = 1;
        $reservation->status = 0;
        $reservation->type = 0;
        $reservation->day = "2018-02-04";
        $reservation->complaint = "Lorem Ipsum is simply dummy text of the printing and typesetting industry.";
        $reservation->created_by = 4;
        $reservation->updated_by = 4;
        $reservation->save();

        $reservation = new Reservation();
        $reservation->user_id = 6;
        $reservation->working_hour_id = 11;
        $reservation->clinic_id = 2;
        $reservation->status = 0;
        $reservation->type = 0;
        $reservation->day = "2018-02-10";
        $reservation->created_by = 4;
        $reservation->updated_by = 4;
        $reservation->save();

        $reservation = new Reservation();
        $reservation->user_id = 5;
        $reservation->working_hour_id = 13;
        $reservation->clinic_id = 2;
        $reservation->status = 1;
        $reservation->type = 0;
        $reservation->day = "2018-02-11";
        $reservation->created_by = 4;
        $reservation->updated_by = 4;
        $reservation->save();

        $reservation = new Reservation();
        $reservation->user_id = 5;
        $reservation->working_hour_id = 11;
        $reservation->clinic_id = 2;
        $reservation->status = 0;
        $reservation->type = 0;
        $reservation->day = "2018-02-12";
        $reservation->complaint = "Lorem Ipsum is simply dummy text of the printing and typesetting industry.";

        $reservation->created_by = 5;
        $reservation->updated_by = 5;
        $reservation->save();


        // account_id 2

        $reservation = new Reservation();
        $reservation->user_id = 25;
        $reservation->working_hour_id = 21;
        $reservation->clinic_id = 3;
        $reservation->status = 0;
        $reservation->type = 0;
        $reservation->day = "2018-02-03";
        $reservation->created_by = 24;
        $reservation->updated_by = 24;
        $reservation->save();

        $reservation = new Reservation();
        $reservation->user_id = 25;
        $reservation->working_hour_id = 23;
        $reservation->clinic_id = 3;
        $reservation->status = 0;
        $reservation->type = 0;
        $reservation->day = "2018-02-18";
        $reservation->created_by = 24;
        $reservation->updated_by = 24;
        $reservation->save();

        $reservation = new Reservation();
        $reservation->user_id = 25;
        $reservation->working_hour_id = 38;
        $reservation->clinic_id = 4;
        $reservation->status = 0;
        $reservation->type = 0;
        $reservation->day = "2018-02-07";
        $reservation->created_by = 24;
        $reservation->updated_by = 24;
        $reservation->save();

        $reservation = new Reservation();
        $reservation->user_id = 26;
        $reservation->working_hour_id = 39;
        $reservation->clinic_id = 4;
        $reservation->status = 0;
        $reservation->type = 0;
        $reservation->day = "2018-02-07";
        $reservation->complaint = "Lorem Ipsum is simply dummy text of the printing and typesetting industry.";
        $reservation->created_by = 26;
        $reservation->updated_by = 26;
        $reservation->save();

        $reservation = new Reservation();
        $reservation->user_id = 26;
        $reservation->working_hour_id = 44;
        $reservation->clinic_id = 5;
        $reservation->status = 0;
        $reservation->type = 0;
        $reservation->day = "2018-02-25";
        $reservation->complaint = "Lorem Ipsum is simply dummy text of the printing and typesetting industry.";
        $reservation->created_by = 24;
        $reservation->updated_by = 24;
        $reservation->save();

    }
}
