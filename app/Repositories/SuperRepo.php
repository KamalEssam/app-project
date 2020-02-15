<?php

namespace App\Repositories;

use App\Traits\EmailTrait;
use App\Traits\ExceptionTrait;
use App\Traits\LangTrait;
use App\Traits\PlanTrait;
use App\Traits\QueueFirebaseTrait;
use App\Traits\SuperTrait;

class SuperRepo
{
    use SuperTrait, EmailTrait, LangTrait, ExceptionTrait, PlanTrait, QueueFirebaseTrait;
}