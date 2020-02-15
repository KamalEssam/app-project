<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->call(CountrySeeder::class);
        $this->call(CitySeed::class);
        $this->call(PlanSeed::class);
        $this->call(AccountSeed::class);
        $this->call(RoleSeed::class);
        $this->call(ClinicSeed::class);
        $this->call(UserSeed::class);
        $this->call(WorkingHourSeed::class);
        $this->call(ReservationSeed::class);
        $this->call(VisitSeed::class);
        $this->call(CommentSeed::class);
        $this->call(MedicationSeed::class);
        $this->call(SpecialitySeed::class);
        $this->call(DoctorDetailSeed::class);
        $this->call(SettingSeed::class);
        $this->call(MedicationVisitSeed::class);
        $this->call(ServiceSeed::class);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');


//        $this->call(AttachmentSeed::class);

    }
}
