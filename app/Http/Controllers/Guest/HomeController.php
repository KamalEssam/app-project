<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\App;
use App\Models\Plan;
use App\Models\Shared\Department;
use App\Http\Requests\DepartmentRequest;
use Illuminate\Http\Requess;
use Flashy;
use Super;

class HomeController extends Controller
{
    public function __construct()
    {

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function newsletterMail(){
        return view('newsletter-mail');
    }
    public function registerMail(){
        return view('register-mail');
    }
    public function test(){
        return view('test');
    }
    public function index(){
        return view('frontend.index');
    }
    public function accountApp($unique_id){
//        $account = Account::where('unique_id',$unique_id)->first();
//
//        if (!$account){
//            abort('404');
//        }
//        $app = App::where('account_id', $account->id)->first();
//        if (!$app){
//            abort('404');
//        }
//        return view('frontend.account-app', compact('app'));
    }


    public function plans(){
        $plans = Plan::all();
        return view('admin.plans',compact('plans'));
    }
}
