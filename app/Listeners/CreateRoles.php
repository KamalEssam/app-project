<?php

namespace App\Listeners;

use App\Events\UserGenerated;
use App\Http\Controllers\ApiController;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateRoles
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserGenerated $event
     * @return bool
     */
    public function handle(UserGenerated $event)
    {
        switch ($event->type) {
            case 1 :
                // doctor
                $event->user->role_id = ApiController::ROLE_DOCTOR;
                break;
            case 2 :
                // assistant
                $event->user->role_id = ApiController::ROLE_ASSISTANT;
                break;
            case 3 :
                // user
                $event->user->role_id = ApiController::ROLE_USER;
                break;
            default :
                // default action
                return false;
        }
    }
}
