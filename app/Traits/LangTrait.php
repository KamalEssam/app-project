<?php

namespace App\Traits;
use App;


trait LangTrait
{
    public function setLang($request){
        if ($request->lang) {
            if ($request->lang == "ar") {
                App::setLocale('ar');
            } else {
                App::setLocale('en');
            }
        }
    }
}