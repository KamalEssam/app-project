<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Repositories\Api\AuthRepository;
use App\Http\Repositories\Api\NotificationRepository;
use App\Http\Repositories\Api\ReservationRepository;
use App\Http\Repositories\Web\TokenRepository;
use App\Http\Traits\NotificationTrait;
use App\Models\Notification;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Validator;

class NotificationController extends ApiController
{
    use NotificationTrait;

    public function __construct(Request $request)
    {
        $this->fail = new \stdClass();
        $this->setLang($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function notificationList(Request $request)
    {
        $user = auth()->guard('api')->user();
        if (!$user) {
            return self::jsonResponse(false, self::CODE_UNAUTHORIZED, trans('lang.unauthorized'), new \stdClass(), new \stdClass());
        }

        $offset = 0;
        if (isset($request->offset)) {
            $offset = $request->offset;
        }

        $limit = 10;
        if (isset($request->limit)) {
            $limit = $request->limit;
        }

        if ($user->role_id == self::ROLE_USER) {
            // get list of notifications of user
            $notifications = (new NotificationRepository())->getUserListOfNotifications($user->id, $offset, $limit);
            if (!$notifications) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.notifications_list'), new \stdClass(), new \stdClass());
            }
        } else if ($user->role_id == self::ROLE_ASSISTANT) {
            $clinic_id = ($user->clinic_id) ?? '';
            // get list of notifications of assistant
            $notifications = (new NotificationRepository())->getAdminNotification($multicast = $user->role_id, $clinic_id, $offset, $limit);
            if (!$notifications) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.notifications_list'), new \stdClass(), new \stdClass());
            }
        } else if ($user->role_id == self::ROLE_DOCTOR) {
            // notifications of poly clinic
            $notifications = (new NotificationRepository())->getAdminNotification($multicast = $user->role_id, $user->id, $offset, $limit);
            if (!$notifications) {
                return self::jsonResponse(false, self::CODE_FAILED, trans('lang.notifications_list'), new \stdClass(), new \stdClass());
            }
        }
        // get notifications without sender and receiver information
        $old_notificaions = $notifications->get()->makeHidden('sender')->makeHidden('receiver');
        return self::jsonResponse(true, self::CODE_OK, trans('lang.notifications_list'), new \stdClass(), $old_notificaions);
    }

    /**
     *  get notifications count
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNotificationsCount(Request $request)
    {
        $user = auth()->guard('api')->user();
        if (!$user) {
            return self::jsonResponse(false, self::CODE_UNAUTHORIZED, trans('lang.unauthorized'), new \stdClass(), new \stdClass());
        }
        // get list of notifications of user
        $notifications_count = (new NotificationRepository())->getUserNotificationsCount($user->id);
        $notifications = new \stdClass();
        $notifications->count = $notifications_count;
        return self::jsonResponse(true, self::CODE_OK, trans('lang.notifications_count'), new \stdClass(), $notifications);
    }

    /**
     * @param Request $request
     * @return bool|\Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function pushAdminNotification(Request $request)
    {
        // Validation area
        $validator = Validator::make($request->all(), [
            'sender_id' => 'required|exists:users,id',
            'receiver_id' => 'required',
            'en_title' => 'required',
            'ar_title' => 'required',
            'ar_message' => 'required',
            'en_message' => 'required',
            'object_id' => 'required',
            'url' => 'required',
            'table' => 'required',
        ]);


        if ($validator->fails()) {
            return self::jsonResponse(false, self::CODE_VALIDATION, $validator->errors()->first(), 'Error in validation', $validator->errors());
        }

        $sender_account = (new AuthRepository())->getUserById($request->sender_id);
        if (!$sender_account) {
            return self::jsonResponse(false, self::CODE_NOT_FOUND, trans('lang.user-not-found'), [], $this->fail);
        }

        if (!isset($request['multicast'])) {
            // create notification to be pushed to all assistants managing system
            $request['multicast'] = 2;
        }

        try {
            $notification = (new NotificationRepository())->createNewNotification($request->all());
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }

        // get an array of tokens
        $reservation = (new ReservationRepository())->getReservationById($request['object_id']);

        $lang = 'en';
        if ($request['multicast'] == 2) {
            // in case of multicses == 2
            // then the receiver_id will be the clinic_id
            $tokens = array_unique(
                (new TokenRepository())->getAnArrayOfTokensByRoleAndAccount(self::ROLE_ASSISTANT, $reservation->clinic->account_id, $request['receiver_id'])
            );
            $assistant = User::where('role_id', self::ROLE_ASSISTANT)->where('clinic_id', $request['receiver_id'])->first();
            if ($assistant) {
                $lang = $assistant->lang;
            }
        } else if ($request['multicast'] == 1) {
            // notification for single user
            // notifications for poly clinic Doctor
            $tokens = array_unique(
                (new TokenRepository())->getAnArrayOfTokensByRoleAndAccount(self::ROLE_DOCTOR, $reservation->clinic->account_id)
            );
            $doctor = (new AuthRepository())->getUserById($request['receiver_id']);
            if ($doctor) {
                $lang = $doctor->lang;
            }
        }

        if (!$tokens) {
            return self::jsonResponse(false, self::CODE_NOT_FOUND, trans('lang.assistants-not-found'), [], $this->fail);
        }
        $this->push_notification($notification[$lang . '_title'], $notification[$lang . '_message'], $tokens, $notification->table, $notification);

//        // create notification to be pushed to doctor managing system
//        $request['multicast'] = 0;
//        try {
//            $notification = (new NotificationRepository())->createNewNotification($request->all());
//        } catch (\Exception $e) {
//            self::logErr($e->getMessage());
//        }
//        if (!$notification) {
//            return self::jsonResponse(false, self::CODE_NOT_FOUND, trans('lang.notification-not-found'), [], $this->fail);
//        }
//
//        $reservation = (new ReservationRepository())->getReservationById($request['object_id']);
//        $doctor_token = array_unique(
//            (new TokenRepository())->getAnArrayOfTokensByRoleAndAccount(self::ROLE_DOCTOR, $reservation->clinic->account_id)
//        );
//
//        if (!$doctor_token) {
//            return self::jsonResponse(false, self::CODE_NOT_FOUND, trans('lang.user-not-found'), [], $this->fail);
//        }
//        $this->push_notification($notification[app()->getLocale() . '_title'], $request->en_message, $doctor_token, $notification->table, $notification);

        return self::jsonResponse(true, self::CODE_OK, trans('lang.notification-found-successfully'), [], $notification);

    }

    /**
     *  set the notification as read
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setNotificationRead(Request $request)
    {
        // Validation area
        $validator = Validator::make($request->all(), [
            'notification_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return self::jsonResponse(false, self::CODE_NOT_FOUND, trans('lang.notification-not-found'), $validator->errors());
        }

        $notifications = Notification::find($request['notification_id']);

        if (!$notifications) {
            return self::jsonResponse(false, self::CODE_NOT_FOUND, trans('lang.notification-not-found'), $validator->errors());
        }

        $notifications->update(['is_read' => 1]);
        return self::jsonResponse(true, self::CODE_OK, trans('lang.notifications'));
    }
}
