<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;


use App\Http\Interfaces\Web\TokenInterface;
use App\Models\Token;

class TokenRepository extends ParentRepository implements TokenInterface
{
    public $token;

    public function __construct()
    {
        $this->token = new Token();
    }

    /**
     * get user tokens
     *
     * @param $user_id
     * @return mixed
     */
    public function getTokensByUserId($user_id)
    {
        try {
            return $this->token->where('user_id', $user_id)->pluck('token')->toArray();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get the token by user_id and the browser
     *
     * @param $user_id
     * @param $browser
     * @return mixed
     */
    public function getTokenByUserIdAndBrowser($user_id, $browser)
    {
        try {
            return $this->token->where('user_id', $user_id)
                ->where('browser', $browser)->first();

        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  create new Token
     *
     * @param $request
     * @return mixed
     */
    public function createToken($request)
    {
        try {
            return $this->token->create($request->all());
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  update the token with the new token
     *
     * @param $token_row
     * @param $the_token
     * @return mixed
     */
    public function updateToken($token_row, $the_token)
    {
        try {
            $updated_token = $token_row->update([
                'token' => $the_token
            ]);

            return $updated_token;
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * @param $role_id
     * @param string $account_id
     * @param string $clinic_id
     * @return mixed
     */
    public function getAnArrayOfTokensByRoleAndAccount($role_id, $account_id = '', $clinic_id = '')
    {
        try {
            return $this->token->join('users', 'users.id', '=', 'tokens.user_id')
                ->where('users.role_id', $role_id)
                ->where(function ($query) use ($account_id, $clinic_id) {
                    if ($account_id != '' || !empty($account_id)) {
                        $query->where('users.account_id', $account_id);
                    }
                    if ($clinic_id != '' || !empty($clinic_id)) {
                        $query->where('users.clinic_id', $clinic_id);
                    }
                })
                ->select('tokens.token')
                ->get()
                ->pluck('token')
                ->toArray();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return array();
        }
    }
}