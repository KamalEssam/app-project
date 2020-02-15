<?php

namespace App\Http\Traits;


trait SmsTrait
{

    public static function sendRklinicSmsMessage($mobile, $msg, $lang = '')
    {
        $post = [
            'recipients' => '2' . $mobile,
            'originator' => 'seena',
            'body' => $msg,
        ];

        // dont send sms in case of localhost or test droplet
        if (in_array(route('admin'), ["http://localhost:8000", "http://178.128.195.150", "https://rklinic-admin.com"])) {
            return true;
        } else {
            $url = 'https://rest.messagebird.com/messages?';


            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization:AccessKey 6MG3jQnNtClO96zyAdoxFyMt5'
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            $response = curl_exec($ch);
            \Log::info('hamdaf' . $response);
            return true;
        }

    }

    public static function sendRandomSmsMessage($mobile, $msg, $lang)
    {
        $post = [
            'recipients' => '2' . $mobile,
            'originator' => 'seena',
            'body' => $msg,
        ];

        // dont send sms in case of localhost or test droplet
        if (in_array(route('admin'), ["http://localhost:8000", "http://178.128.195.150", "https://rklinic-admin.com"])) {
            return true;
        } else {
            $url = 'https://rest.messagebird.com/messages?';


            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization:AccessKey 6MG3jQnNtClO96zyAdoxFyMt5'
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            $response = curl_exec($ch);
            \Log::info('hamdaf' . $response);
            return true;
        }
    }

}
