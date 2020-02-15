<?php

use Illuminate\Database\Seeder;
use App\Models\Clinic;

class ClinicSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {

        $clinic = new Clinic();
        $clinic->en_address = "14 clinic street 1";
        $clinic->ar_address = "14 شارع العيادة 1";
        $clinic->lat = 30.1132324;
        $clinic->lng = 31.1132324;
        $clinic->pattern = 0;
        $clinic->fees = random_int(90,150);
        $clinic->follow_up_fees = random_int(30,90);
        $clinic->created_by = 3;
        $clinic->updated_by = 3;
        $clinic->account_id = 1;
        $clinic->save();


        $clinic = new Clinic();
        $clinic->en_address = "14 clinic street 2";
        $clinic->ar_address = "14 شارع العيادة 2";
        $clinic->lat = 30.2232324;
        $clinic->lng = 31.2232324;
        $clinic->pattern = 0;
        $clinic->fees = random_int(90,150);
        $clinic->follow_up_fees = random_int(30,90);
        $clinic->created_by = 3;
        $clinic->updated_by = 3;
        $clinic->account_id = 1;
        $clinic->save();

        $clinic = new Clinic();
        $clinic->en_address = "14 clinic street 2";
        $clinic->ar_address = "14 شارع العيادة 2";
        $clinic->lat = 30.1132324;
        $clinic->lng = 31.1132324;
        $clinic->pattern = 0;
        $clinic->fees = random_int(90,150);
        $clinic->follow_up_fees = random_int(30,90);
        $clinic->created_by = 23;
        $clinic->updated_by = 23;
        $clinic->account_id = 2;
        $clinic->save();

        $clinic = new Clinic();
        $clinic->en_address = "14 clinic street 1";
        $clinic->ar_address = "14 شارع العيادة 1";
        $clinic->lat = 30.2232324;
        $clinic->lng = 31.2232324;
        $clinic->pattern = 1;
        $clinic->fees = random_int(90,150);
        $clinic->follow_up_fees = random_int(30,90);
        $clinic->created_by = 23;
        $clinic->updated_by = 23;
        $clinic->account_id = 2;
        $clinic->save();

        $clinic = new Clinic();
        $clinic->en_address = "14 clinic street 3";
        $clinic->ar_address = "14 شارع العيادة 3";
        $clinic->lat = 30.3332324;
        $clinic->lng = 31.3332324;
        $clinic->pattern = 1;
        $clinic->fees = random_int(90,150);
        $clinic->follow_up_fees = random_int(30,90);
        $clinic->created_by = 23;
        $clinic->updated_by = 23;
        $clinic->account_id = 2;
        $clinic->save();
    }

}
