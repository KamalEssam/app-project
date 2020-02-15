<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface TokenInterface
{
    /**
     * get user tokens
     *
     * @param $user_id
     * @return mixed
     */
    public function getTokensByUserId($user_id);

    /**
     *  get the token by user_id and the browser
     *
     * @param $user_id
     * @param $browser
     * @return mixed
     */
    public function getTokenByUserIdAndBrowser($user_id, $browser);

    /**
     *  create new Token
     *
     * @param $request
     * @return mixed
     */
    public function createToken($request);

    /**
     *  update the token with the new token
     *
     * @param $token_row
     * @param $the_token
     * @return mixed
     */
    public function updateToken($token_row, $the_token);


    /**
     * @param $role_id
     * @param $account_id
     * @return mixed
     */
    public function getAnArrayOfTokensByRoleAndAccount($role_id, $account_id, $clinic_id);
}