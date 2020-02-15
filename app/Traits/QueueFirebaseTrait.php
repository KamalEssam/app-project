<?php

namespace App\Traits;

trait QueueFirebaseTrait
{
    // get data from a child in firebase
    public function getChildObjects($url, $child)
    {
        // create curl resource
        $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, $url . $child . "/.json");

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up System resources
        curl_close($ch);


        return json_decode($output);
    }
    // post data to a key child
    public function setData($url, $key, $data)
    {
        $data = json_encode($data);
        $url = $url . $key . '/.json';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/plain'));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');


        $jsonResponse = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }
        curl_close($ch);

        return $jsonResponse;
    }
}