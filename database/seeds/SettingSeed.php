<?php

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $row = new Setting();//1
        $row->user_counter = 26;
        $row->account_counter = 3;
        $row->assistant_counter = 3;
        $row->mobile = '01068475881';
        $row->email = 'info@rklinic.com';
        $row->website = 'https://rklinic.com/';
        $row->facebook = 'https://www.facebook.com/rklinic/?modal=admin_todo_tour';
        $row->twitter = 'https://twitter.com/rklinic1';
        $row->youtube = 'https://www.youtube.com/channel/UCYaO_gtdnzqn0KEKTfQLQ4w?view_as=subscriber';
        $row->googlepluse = 'https://rklinic.com/';
        $row->instagram = 'https://www.instagram.com/rklinic_official/?utm_source=ig_profile_share&igshid=th093vsqcp8y';
        $row->en_about_us = 'A Doctor or a patient, what could be better than a custom made Application just for you? RKlinic has just created the right-in-your-pocket application that will help both doctors and their patients to communicate easier and better. Get to know your Application and its features.
        Our application was made for doctors who care, work and strive to help people and people alone! That alone will help doctors to create a more comfortable environment for patients to feel closer and more related to the entire medical process.';
        $row->ar_about_us = 'طبيب أو مريض ، ماذا يمكن أن يكون أفضل من تطبيق مخصص مخصص لك فقط؟ RKlinic قد أنشأت للتو تطبيق الحق في جيبك الذي سيساعد كل من الأطباء ومرضاهم على التواصل أسهل وأفضل. تعرّف على تطبيقك وميزاته.
        تم تقديم طلبنا للأطباء الذين يهتمون ويعملون ويسعون لمساعدة الناس والناس بمفردهم! هذا وحده سيساعد الأطباء على خلق بيئة أكثر راحة للمرضى لكي يشعروا بأنهم أقرب وأكثر وأكثر ارتباطًا بالعملية الطبية بأكملها.';
        $row->save();
    }
}
