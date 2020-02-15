<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->name = 'RKSuperAdmin';
        $user->email = 'rksuperadmin@admin-seena.com';
        $user->password = 'password';
        $user->gender = 0;
        $user->birthday = "1994-11-19";
        $user->mobile = '0125' . mt_rand(1000000,9999999);
        $user->is_active = 1;
        $user->role_id = 5;
        $user->save();

        $user = new User();
        $user->name = 'RKadmin';
        $user->email = 'rkadmin@admin-seena.com';
        $user->password = 'password';
        $user->gender = 0;
        $user->birthday = "1994-11-19";
        $user->mobile = '0125' . mt_rand(1000000,9999999);
        $user->is_active = 1;
        $user->role_id = 4;
        $user->save();

        $user = new User();
        $user->name = 'Doctor';
        $user->email = 'doctor@clinic.com';
        $user->password = 'password';
        $user->gender = 0;
        $user->birthday = "1994-11-19";
        $user->mobile = '0125' . mt_rand(1000000,9999999);
        $user->is_active = 1;
        $user->role_id = 1;
        $user->is_active = 1;
        $user->unique_id = "RK_ACC_1000";
        $user->account_id = 1;
        $user->save();

        $user = new User();
        $user->name = 'assistant';
        $user->email = 'assistant@clinic.com';
        $user->password = 'password';
        $user->gender = 0;
        $user->birthday = "1994-11-19";
        $user->mobile = '0125' . mt_rand(1000000,9999999);
        $user->is_active = 1;
        $user->role_id = 2;
        $user->unique_id = "RK_CO_1000";
        $user->clinic_id = 1;
        $user->is_active = 1;
        $user->account_id = 1;
        $user->created_by = 3;
        $user->save();

        $user = new User();
        $user->name = 'user_1';
        $user->email = 'user_1@clinic.com';
        $user->password = 'password';
        $user->gender = 0;
        $user->birthday = "1994-11-19";
        $user->mobile = '0125' . mt_rand(1000000,9999999);
        $user->is_active = 1;
        $user->role_id = 3;
        $user->unique_id = "1000";
        $user->save();

        $user = new User();
        $user->name = 'user_2';
        $user->email = 'user_2@clinic.com';
        $user->password = 'password';
        $user->gender = 0;
        $user->birthday = "1994-11-19";
        $user->mobile = '0125' . mt_rand(1000000,9999999);
        $user->is_active = 1;
        $user->role_id = 3;
        $user->unique_id = "1001";
        $user->save();

        $user = new User();
        $user->name = 'user_3';
        $user->email = 'user_3@clinic.com';
        $user->password = 'password';
        $user->gender = 0;
        $user->birthday = "1994-11-19";
        $user->mobile = '0125' . mt_rand(1000000,9999999);
        $user->is_active = 1;
        $user->role_id = 3;
        $user->unique_id = "1002";
        $user->account_id = 1;
        $user->save();

        $user = new User();
        $user->name = 'user_4';
        $user->email = 'user_4@clinic.com';
        $user->password = 'password';
        $user->gender = 0;
        $user->birthday = "1994-11-19";
        $user->mobile = '0125' . mt_rand(1000000,9999999);
        $user->is_active = 1;
        $user->role_id = 3;
        $user->unique_id = "1003";
        $user->save();

        $user = new User();
        $user->name = 'user_5';
        $user->email = 'user_5@clinic.com';
        $user->password = 'password';
        $user->gender = 0;
        $user->birthday = "1994-11-19";
        $user->mobile = '0125' . mt_rand(1000000,9999999);
        $user->is_active = 1;
        $user->role_id = 3;
        $user->unique_id = "1004";
        $user->save();

        $user = new User();
        $user->name = 'user_6';
        $user->email = 'user_6@clinic.com';
        $user->password = 'password';
        $user->gender = 0;
        $user->birthday = "1994-11-19";
        $user->mobile = '0125' . mt_rand(1000000,9999999);
        $user->is_active = 1;
        $user->role_id = 3;
        $user->unique_id = "1005";
        $user->save();

        $user = new User();
        $user->name = 'user_7';
        $user->email = 'user_7@clinic.com';
        $user->password = 'password';
        $user->gender = 0;
        $user->birthday = "1994-11-19";
        $user->mobile = '0125' . mt_rand(1000000,9999999);
        $user->is_active = 1;
        $user->role_id = 3;
        $user->unique_id = "1006";
        $user->save();

        $user = new User();
        $user->name = 'user_8';
        $user->email = 'user_8@clinic.com';
        $user->password = 'password';
        $user->gender = 0;
        $user->birthday = "1994-11-19";
        $user->mobile = '0125' . mt_rand(1000000,9999999);
        $user->is_active = 1;
        $user->role_id = 3;
        $user->unique_id = "1007";
        $user->save();

        $user = new User();
        $user->name = 'user_9';
        $user->email = 'user_9@clinic.com';
        $user->password = 'password';
        $user->gender = 0;
        $user->birthday = "1994-11-19";
        $user->mobile = '0125' . mt_rand(1000000,9999999);
        $user->is_active = 1;
        $user->role_id = 3;
        $user->unique_id = "1008";
        $user->save();

        $user = new User();
        $user->name = 'user_10';
        $user->email = 'user_10@clinic.com';
        $user->password = 'password';
        $user->gender = 0;
        $user->birthday = "1994-11-19";
        $user->mobile = '0125' . mt_rand(1000000,9999999);
        $user->is_active = 1;
        $user->role_id = 3;
        $user->unique_id = "1009";
        $user->save();

        $user = new User();
        $user->name = 'user_11';
        $user->email = 'user_11@clinic.com';
        $user->password = 'password';
        $user->gender = 0;
        $user->birthday = "1994-11-19";
        $user->mobile = '0125' . mt_rand(1000000,9999999);
        $user->is_active = 1;
        $user->role_id = 3;
        $user->unique_id = "1010";
        $user->save();

        $user = new User();
        $user->name = 'user_12';
        $user->email = 'user_12@clinic.com';
        $user->password = 'password';
        $user->gender = 0;
        $user->birthday = "1994-11-19";
        $user->mobile = '0125' . mt_rand(1000000,9999999);
        $user->is_active = 1;
        $user->role_id = 3;
        $user->unique_id = "1011";
        $user->save();

        $user = new User();
        $user->name = 'user_13';
        $user->email = 'user_13@clinic.com';
        $user->password = 'password';
        $user->gender = 0;
        $user->birthday = "1994-11-19";
        $user->mobile = '0125' . mt_rand(1000000,9999999);
        $user->is_active = 1;
        $user->role_id = 3;
        $user->unique_id = "1012";
        $user->save();


        $user = new User();
        $user->name = 'user_14';
        $user->email = 'user_14@clinic.com';
        $user->password = 'password';
        $user->gender = 0;
        $user->birthday = "1994-11-19";
        $user->mobile = '0125' . mt_rand(1000000,9999999);
        $user->is_active = 1;
        $user->role_id = 3;
        $user->unique_id = "1013";
        $user->save();


        $user = new User();
        $user->name = 'user_15';
        $user->email = 'user_15@clinic.com';
        $user->password = 'password';
        $user->gender = 0;
        $user->birthday = "1994-11-19";
        $user->mobile = '0125' . mt_rand(1000000,9999999);
        $user->is_active = 1;
        $user->role_id = 3;
        $user->unique_id = "1014";
        $user->save();


        $user = new User();
        $user->name = 'user_16';
        $user->email = 'user_16@clinic.com';
        $user->password = 'password';
        $user->gender = 0;
        $user->birthday = "1994-11-19";
        $user->mobile = '0125' . mt_rand(1000000,9999999);
        $user->is_active = 1;
        $user->role_id = 3;
        $user->unique_id = "1015";
        $user->save();


        $user = new User();
        $user->name = 'user_17';
        $user->email = 'user_17@clinic.com';
        $user->password = 'password';
        $user->gender = 0;
        $user->birthday = "1994-11-19";
        $user->mobile = '0125' . mt_rand(1000000,9999999);
        $user->is_active = 1;
        $user->role_id = 3;
        $user->unique_id = "1016";
        $user->account_id = 1;
        $user->save();


        $user = new User();
        $user->name = 'user_18';
        $user->email = 'user_18@clinic.com';
        $user->password = 'password';
        $user->gender = 0;
        $user->birthday = "1994-11-19";
        $user->mobile = '0125' . mt_rand(1000000,9999999);
        $user->is_active = 1;
        $user->role_id = 3;
        $user->unique_id = "1017";
        $user->save();

        /****************************************Doctor 2****************************************/
        $user = new User();
        $user->name = 'Doctor2';
        $user->email = 'doctor2@clinic.com';
        $user->password = 'password';
        $user->gender = 0;
        $user->birthday = "1994-11-19";
        $user->mobile = '0125' . mt_rand(1000000,9999999);
        $user->is_active = 1;
        $user->role_id = 1;
        $user->is_active = 1;
        $user->unique_id = "RK_ACC_1001";
        $user->account_id = 2;
        $user->save();

        $user = new User();
        $user->name = 'assistant2';
        $user->email = 'assistant2@clinic.com';
        $user->password = 'password';
        $user->gender = 0;
        $user->birthday = "1994-11-19";
        $user->mobile = '0125' . mt_rand(1000000,9999999);
        $user->is_active = 1;
        $user->role_id = 2;
        $user->unique_id = "RK_CO_1001";
        $user->clinic_id = 2;
        $user->account_id = 2;
        $user->created_by = 23;
        $user->save();

        $user = new User();
        $user->name = 'user_19';
        $user->email = 'user_19@clinic.com';
        $user->password = 'password';
        $user->gender = 0;
        $user->birthday = "1994-11-19";
        $user->mobile = '0125' . mt_rand(1000000,9999999);
        $user->is_active = 1;
        $user->role_id = 3;
        $user->unique_id = "1018";
        $user->save();

        $user = new User();
        $user->name = 'user_20';
        $user->email = 'user_20@clinic.com';
        $user->password = 'password';
        $user->gender = 0;
        $user->birthday = "1994-11-19";
        $user->mobile = '0125' . mt_rand(1000000,9999999);
        $user->is_active = 1;
        $user->role_id = 3;
        $user->unique_id = "1019";
        $user->save();

        $user = new User();
        $user->name = 'user_21';
        $user->email = 'user_21@clinic.com';
        $user->password = 'password';
        $user->gender = 0;
        $user->birthday = "1994-11-19";
        $user->mobile = '0125' . mt_rand(1000000,9999999);
        $user->is_active = 1;
        $user->role_id = 3;
        $user->unique_id = "1020";
        $user->save();


        $user = new User();
        $user->name = 'user_22';
        $user->email = 'user_22@clinic.com';
        $user->password = 'password';
        $user->gender = 0;
        $user->birthday = "1994-11-19";
        $user->mobile = '0125' . mt_rand(1000000,9999999);
        $user->is_active = 1;
        $user->role_id = 3;
        $user->unique_id = "1021";
        $user->account_id = 2;
        $user->save();

        $user = new User();
        $user->name = 'user_23';
        $user->email = 'user_23@clinic.com';
        $user->password = 'password';
        $user->gender = 0;
        $user->birthday = "1994-11-19";
        $user->mobile = '0125' . mt_rand(1000000,9999999);
        $user->is_active = 1;
        $user->role_id = 3;
        $user->unique_id = "1022";
        $user->save();

        $user = new User();
        $user->name = 'user_24';
        $user->email = 'user_24@clinic.com';
        $user->password = 'password';
        $user->gender = 0;
        $user->birthday = "1994-11-19";
        $user->mobile = '0125' . mt_rand(1000000,9999999);
        $user->is_active = 1;
        $user->role_id = 3;
        $user->unique_id = "1023";
        $user->save();

        $user = new User();
        $user->name = 'user_25';
        $user->email = 'user_25@clinic.com';
        $user->password = 'password';
        $user->gender = 0;
        $user->birthday = "1994-11-19";
        $user->mobile = '0125' . mt_rand(1000000,9999999);
        $user->is_active = 1;
        $user->role_id = 3;
        $user->unique_id = "1024";
        $user->save();

    }
}
