<?php

namespace App\Http\Traits;

use App\Mail\ContactMailer;
use App\Mail\Mailer;
use Mail;

trait MailTrait
{
    public function sendMailTraitFun($data)  // data must contain [subject , view, to]
    {
        Mail::to($data['to'])->send(new Mailer($data));
    }

    public function sendContactMail($data)  // data must contain [subject , from , view, to]
    {
        Mail::to($data['to'])->send(new ContactMailer($data));
    }
}