<?php

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = new Role();
        $role->name = 'doctor';
        $role->desc = 'owner of the clinic';
        $role->save();

        $role = new Role();
        $role->name = 'assistant';
        $role->desc = 'doctor\'s assistant';
        $role->save();

        $role = new Role();
        $role->name = 'user';
        $role->desc = 'user who use application';
        $role->save();

        $role = new Role();
        $role->name = 'rk-admin';
        $role->desc = 'admin who manage system';
        $role->save();

        $role = new Role();
        $role->name = 'rk-super';
        $role->desc = 'admin who manage sensitive functions in system';
        $role->save();

        $role = new Role();
        $role->name = 'sales';
        $role->desc = 'sales agents';
        $role->save();

        $role = new Role();
        $role->name = 'brand';
        $role->desc = 'sponsor brands';
        $role->save();
    }
}
