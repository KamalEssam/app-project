<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface AssistantInterface
{
    /**
     *  get all assistants in the same account
     *
     * @param $account_id
     * @return mixed
     */
    public function getAssistantsByAccount($account_id);

    /**
     * update the given assistant from request
     *
     * @param $assistant
     * @param $request
     * @return mixed
     */
    public function updateAssistant($assistant, $request);


}