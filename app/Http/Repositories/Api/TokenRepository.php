<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Api;


use App\Http\Interfaces\Api\TokenInterface;
use App\Http\Repositories\Web\ParentRepository;
use App\Models\Token;
use DB;

class TokenRepository extends ParentRepository implements TokenInterface
{
    protected $token;

    public function __construct()
    {
        $this->token = new Token();
    }

    /**
     *  get token by user and serial
     *
     * @param $request
     * @return mixed
     */
    public function getTokenByUserAndSerial($request)
    {
        try {
            $token = $this->token->where('user_id', $request->user()->id)
                ->where('serial', $request['serial'])
                ->first();

            if (empty($token)) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        return $token;
    }

    /**
     * set new token or add new Token
     *
     * @param $request
     * @return bool
     * @throws \Exception
     */
    public function setToken($request)
    {
        DB::beginTransaction();

        try {
            $token = $this->token->firstOrCreate(['serial' => $request['serial']]);
            $token->token = $request['token'];
            $token->user_id = $request->user()->id;
            $token->save();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            DB::rollBack();
            return false;
        }

        DB::commit();
        return $token;
    }

    /**
     *  remove token by serial
     *
     * @param $serial
     * @return mixed
     */
    public function removeToken($serial)
    {
        // TODO: Implement removeToken() method.
    }
}
