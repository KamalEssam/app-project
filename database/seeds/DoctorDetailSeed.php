<?php

use Illuminate\Database\Seeder;
use App\Models\DoctorDetail;

class DoctorDetailSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $row = new DoctorDetail();
        $row->max_hours_to_cancel_reservation = 24;
        $row->en_bio = 'This is first bio';
        $row->ar_bio = 'هذا الوصف الاول';
        $row->speciality_id = 1;
        $row->en_reservation_message = 'Doctor Kareem';
        $row->ar_reservation_message = 'دكتور كريم';
        $row->account_id = 1;
        $row->website = 'www.clinic.com';
        $row->facebook = 'www.facebook.com';
        $row->twitter = 'www.twitter.com';
        $row->linkedin = 'www.linkedin.com';
        $row->youtube = 'www.youtube.com';
        $row->googlepluse = 'www.googlepluse.com';
        $row->instagram = 'www.instagram.com';
        $row->save();

        $row = new DoctorDetail();
        $row->max_hours_to_cancel_reservation = 5;
        $row->en_bio = 'This is Second bio';
        $row->ar_bio = 'هذا الوصف الثانى';
        $row->speciality_id = 2;
        $row->en_reservation_message = 'Doctor Khaled';
        $row->ar_reservation_message = 'دكتور خالد';
        $row->account_id = 2;
        $row->website = 'www.clinic.com';
        $row->facebook = 'www.facebook.com';
        $row->twitter = 'www.twitter.com';
        $row->linkedin = 'www.linkedin.com';
        $row->youtube = 'www.youtube.com';
        $row->googlepluse = 'www.googlepluse.com';
        $row->instagram = 'www.instagram.com';
        $row->save();

    }
}