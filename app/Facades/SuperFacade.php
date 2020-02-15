<?php
namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class SuperFacade extends Facade{
    public static function getFacadeAccessor(){ return 'super'; }
}
