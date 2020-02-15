<?php

namespace App\Traits;

use App\Models\Job;
use App\Models\JobUser;

use Intervention\Image\Facades\Image;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

trait SuperTrait
{

    public function generatePassword($size = 8)
    {
        $p = openssl_random_pseudo_bytes(ceil($size * 0.67), $crypto_strong);
        $p = str_replace('=', '', base64_encode($p));
        $p = strtr($p, '+/', '^*');
        return substr($p, 0, $size);
    }

    public function randomPin()
    {
        return rand(1111, 9999);
    }

    public function shortenText($string, $wordsreturned)
    {
        $string = strip_tags($string);
        $retval = $string;
        $string = preg_replace('/(?<=\S,)(?=\S)/', ' ', $string);
        $string = str_replace("\n", " ", $string);
        $array = explode(" ", $string);
        if (count($array) <= $wordsreturned) {
            $retval = $string;
        } else {
            array_splice($array, $wordsreturned);
            $retval = implode(" ", $array) . " ...";
        }
        return $retval;
    }

    function trim_text($input, $length, $ellipses = true, $strip_html = true)
    {
        //strip tags, if desired
        if ($strip_html) {
            $input = strip_tags($input);
        }

        //no need to trim, already shorter than trim length
        if (strlen($input) <= $length) {
            return $input;
        }

        //find last space within length
        $last_space = strrpos(substr($input, 0, $length), ' ');
        $trimmed_text = substr($input, 0, $last_space);

        //add ellipses (...)
        if ($ellipses) {
            $trimmed_text .= '...';
        }

        return $trimmed_text;
    }

    public function validate($rules, $request)
    {
        if (is_array($rules)) {
            foreach ($rules as $rule) {
                if (!$request[$rule]) {
                    return response()->json([
                        'Error' => ['type' => 'fail', 'desc' => $rule . ' is_required', 'code' => 11],
                        'Response' => new \stdClass()
                    ]);
                }
            }
        }
    }

    public function jsonResponse($status, $error_code, $message, $validation = "", $response = "", $token = "")
    {

        $response = empty($response) ? new \stdClass() : $response;
        $validation = empty($validation) ? new \stdClass() : $validation;

        return response()->json([
            'Error' => [
                'status' => $status,
                'code' => $error_code,
                'validation' => $validation,
                'desc' => $message,
                'token' => $token
            ],
            'Response' => $response,
        ], 200);
    }

    public function getProperty($value)
    {
        if (!$value || $value == null || empty($value || !isset($value))) {
            echo 'Not Set';
        } else {
            echo $value;
        }
    }

    public function calculateDueAmount($systems, $plan)
    {
        $due_amount = 0;
        if (!is_array($systems)) {
            return false;
        }
        foreach ($systems as $system) {
            $due_amount += $plan->price;
        }
        return $due_amount;
    }

    // function takes the address name and minimize it
    public function min_address($address, $limit = 20)
    {
        if (count_chars($address) > $limit) {
            return substr($address, 0, $limit) . '...';
        }
        return $address;
    }


    /**
     *  put the width of an image
     *
     * @param $image
     * @param int $width
     * @return mixed
     */
    public function putImageSize($image, $width = 0)
    {
        if ($width != 0) {

            return str_replace('[w]', $width, $image);
        }
        return $image;

    }


    /**
     *  get the image width
     * @param $name
     * @return mixed
     */
    public function getImageWithWidth($name)
    {
        try {
            $source_image = asset('/assets/images/offers/' . $name);
            // create new Intervention Image
            $img = Image::make($source_image);
            try {

                if ( ($_GET['width'] == '[w]') || ($_GET['height'] == '[h]')) {
                    return $img->response();
                } else if (isset($_GET['width']) && isset($_GET['height'])) {
                    $img->resize($_GET['width'], $_GET['height']);
                }

            } catch (Exception $e) {
                return $img->response();
            }
            return $img->response();

        } catch (Exception $e) {

            $source_image = asset('/assets/images/default.png');
            $img = Image::make($source_image);

            try {
                if (isset($_GET['width'])) {
                    $img->resize($_GET['width'], null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }

            } catch (Exception $e) {
                return $img->response();
            }
            return $img->response();
        }
    }

}
