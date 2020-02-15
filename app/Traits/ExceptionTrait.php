<?php

namespace App\Traits;


use App\Exceptions\CustomException;
use App\Exceptions\InternalErrorException;
use App\Exceptions\UnauthorizedException;
use Illuminate\Database\QueryException;

trait ExceptionTrait
{
    public function catchExceptions($exception)
    {
        if ($exception instanceof QueryException) {
            //throw new CustomException($exception->getMessage());
            throw new CustomException('Database unexpected error, we\'ll be right back real quick');
        }
        elseif ($exception instanceof UnauthorizedException) {
            throw new CustomException('Unknown unexpected error, we\'ll be right back real quick');
        } elseif ($exception instanceof InternalErrorException) {
            throw new CustomException('Whoops seems maintenance work is going on, we\'ll be right back real quick');
        }
        throw new CustomException('Something went wrong, we\'ll be right back real quick');
    }
}