<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;


use App\Http\Controllers\WebController;
use App\Http\Interfaces\Web\AssistantInterface;
use App\Models\User;

class AssistantRepository extends ParentRepository implements AssistantInterface
{
    protected $assistant;

    public function __construct()
    {
        $this->assistant = new User();
    }

    /**
     *  get all assistants in the same account
     *
     * @param $account_id
     * @return mixed
     */
    public function getAssistantsByAccount($account_id)
    {
        try {
            return $this->assistant->where('role_id', WebController::ROLE_ASSISTANT)->where('account_id', $account_id)->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * update the given assistant from request
     *
     * @param $assistant
     * @param $request
     * @return mixed
     */
    public function updateAssistant($assistant, $request)
    {
        try {

            return $assistant->update($request->all());
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }
}