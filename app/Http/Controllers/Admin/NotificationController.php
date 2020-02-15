<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\AuthRepository;
use App\Http\Repositories\Web\NotificationRepository;
use App\Http\Repositories\Web\ReservationRepository;
use App\Http\Repositories\Web\TokenRepository;
use App\Http\Repositories\Web\WorkingHourRepository;
use App\Http\Traits\NotificationTrait;
use App\Http\Traits\SmsTrait;
use App\Http\Traits\UserTrait;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Super;
use DB;

class NotificationController extends WebController
{
    use NotificationTrait, SmsTrait, UserTrait;

    /**
     *  set new token or update the old Token
     *
     * @param Request $request
     * @return string
     * @throws \Exception
     */
    public function setToken(Request $request)
    {
        $token_repo = new TokenRepository();
        $auth_user = auth()->user();

        if ($auth_user != null) {
            $is_token = $token_repo->getTokenByUserIdAndBrowser($auth_user->id, $request->browser);
            DB::beginTransaction();
            if ($is_token) {
                try {
                    $token_repo->updateToken($is_token, $request->notification_token);
                } catch (\Exception $e) {
                    DB::rollBack();
                    self::logErr($e->getMessage());
                    return 'false';
                }
            } else {
                $request['platform'] = 1;
                $request['user_id'] = $auth_user->id;
                try {
                    $token_repo->createToken($request);
                } catch (\Exception $e) {
                    DB::rollBack();
                    self::logErr($e->getMessage());
                    return 'false';
                }
            }

            DB::commit();
            return 'true';
        }
        return 'false';
    }

    /**
     *
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $notification = (isset($_GET['notification']) && is_numeric($_GET['notification'])) ? $_GET['notification'] : '';
        $notifications = (new NotificationRepository())->getNotificationListWeb(1, $notification)->take(5);
        return view('admin.notifications.notifications-list', compact('notifications'));
    }

    /**
     *  load more button
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function loadMore(Request $request)
    {
        $auth = auth()->user();
        $multicast = $auth->role_id;

        $offset = 0;
        if (isset($request->offset) && is_numeric($request->offset)) {
            $offset = $request->offset;
        }
        // get the notifications
        $notifications = (new NotificationRepository())->getUserNotificationsUsingOffset($auth, $multicast, $offset);

        if (!$notifications) {
            $notifications = new Collection();
        }

        return response()->json(['data' => view('admin.notifications.notification-box', compact('notifications'))->render(), 'count' => $notifications->count()], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function lastNotificationClick()
    {
        $update_user = (new AuthRepository())->updateLastNotification(auth()->user());
        if (!$update_user) {
            return response()->json(['status' => false], 500);
        }
        return response()->json(['status' => true], 200);
    }

    /**
     *  the counter Box
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function counterBox()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['status' => false], 500);
        }
        $notifications = (new NotificationRepository())->getUnReadNotification($user);
        if (!$notifications) {
            return response()->json(['status' => false], 500);
        }
        return view('includes.admin.counter-box', compact('notifications'));
    }

    // when assistant change or reset working hours , reservation in this working hours canceled and push notification to patients

    /**
     * @param $clinic_id
     * @param $pattern
     * @param null $day
     * @param $start_date
     * @param $end_date
     */
    public function canceledWhenChangeWorkingHours($clinic_id, $pattern, $day = null, $start_date = null, $end_date = null)
    {

        $working_hours_changed = (new WorkingHourRepository())->getArrayOfTrashedWorkingHours($clinic_id);

        $cancel_reservations = (new ReservationRepository())->getReservationUsingTrashedWorkingHours($working_hours_changed, $pattern, $day, $start_date, $end_date);

        return $this->loopReservationsAndCancelIt($cancel_reservations);
    }

    /**
     *  cancel reservations in clinic from particular data
     *
     * @param $clinic_id
     * @param $dayName
     * @param $starting_date
     */
    public function cancelReservationsFromStartingDate($clinic_id, $dayName, $starting_date)
    {

        $cancel_reservations = (new ReservationRepository())->getReservationsListInClinicAndDay($clinic_id, $dayName, $starting_date);

        $this->loopReservationsAndCancelIt($cancel_reservations);
    }

    /**
     *  cancel given reservations
     *
     * @param $cancel_reservations
     */
    public function loopReservationsAndCancelIt($cancel_reservations)
    {
        $super_admin = (new AuthRepository())->getSuperAdmin();
        if (count($cancel_reservations) > 0) {
            foreach ($cancel_reservations as $cancel_reservation) {
                $cancel_reservation->status = self::R_STATUS_CANCELED;
                $cancel_reservation->update();
                // send sms to be pushed to patient

                $patient = self::getUserById($cancel_reservation->user_id);

                if (app()->getLocale() == 'en') {
                    $msg = 'Your reservation has been canceled because of changing working hours';
                    $lang = self::LANG_EN;
                } else {
                    $msg = 'لقد تم الغاء حجزك بسبب تغيير المواعيد';
                    $lang = self::LANG_AR;
                }

                // send SMS Message
                try {
                    self::sendRklinicSmsMessage($patient->mobile, $msg, $lang);
                } catch (\Exception $e) {
                    self::logErr($e->getMessage());
                }

                $notification_data = [
                    'multicast' => 0,
                    'sender_id' => $super_admin->id,
                    'receiver_id' => $cancel_reservation->user_id,
                    'en_title' => auth()->user()->account['en_name'],
                    'ar_title' => auth()->user()->account['ar_name'],
                    'en_message' => 'Your reservation has been canceled because of changing working hours',
                    'ar_message' => 'لقد تم الغاء حجزك بسبب تغيير المواعيد',
                    'url' => 'reservations',
                    'object_id' => $cancel_reservation->id,
                    'table' => 'reservation',
                ];
                $notification = (new NotificationRepository())->createNewNotification($notification_data);

                $lang = (new \App\Http\Repositories\Api\AuthRepository())->getUserById($cancel_reservation->user_id)->lang;
                if (!$lang) {
                    $lang = 'en';
                }
                $tokens = (new TokenRepository())->getTokensByUserId($notification->receiver_id);
                $this->push_notification($notification[$lang . '_title'], $notification[$lang . '_message'], $tokens, $notification->url, $notification);
            }
        }
    }

    /**
     *  send notifications
     *
     * @param $reciever_id
     * @param $ar_title
     * @param $en_title
     * @param $en_message
     * @param $ar_message
     */
    public function sendNotification($reciever_id, $ar_title, $en_title, $en_message, $ar_message)
    {
        $notification_data = [
            'multicast' => self::ROLE_DOCTOR,
            'sender_id' => 1,
            'receiver_id' => $reciever_id,
            'en_title' => $en_title,
            'ar_title' => $ar_title,
            'en_message' => $en_message,
            'ar_message' => $ar_message,
            'url' => '#',
            'object_id' => '',
            'table' => 'notifications',
        ];
        $notification = (new NotificationRepository())->createNewNotification($notification_data);

        $lang = (new \App\Http\Repositories\Api\AuthRepository())->getUserById($reciever_id)->lang;
        if (!$lang) {
            $lang = 'en';
        }
        $tokens = (new TokenRepository())->getTokensByUserId($notification->receiver_id);
        $this->push_notification($notification[$lang . '_title'], $notification[$lang . '_message'], $tokens, $notification->url, $notification);
    }

    /**
     *  send notifications to review reservations
     *
     * @param $reciever_id
     * @param $ar_title
     * @param $en_title
     * @param $en_message
     * @param $ar_message
     * @param $object_id
     * @param string $table
     */
    public function sendNotificationToReviewReservation($reciever_id, $ar_title, $en_title, $en_message, $ar_message, $object_id, $table = 'review')
    {
        $notification_data = [
            'multicast' => 0,
            'sender_id' => 1,
            'receiver_id' => $reciever_id,
            'en_title' => $en_title,
            'ar_title' => $ar_title,
            'en_message' => $en_message,
            'ar_message' => $ar_message,
            'url' => '#',
            'object_id' => $object_id,
            'table' => $table,
        ];
        $notification = (new NotificationRepository())->createNewNotification($notification_data);

        $lang = (new \App\Http\Repositories\Api\AuthRepository())->getUserById($reciever_id)->lang;
        if (!$lang) {
            $lang = 'en';
        }
        $tokens = (new TokenRepository())->getTokensByUserId($notification->receiver_id);
        $this->push_notification($notification[$lang . '_title'], $notification[$lang . '_message'], $tokens, $notification->url, $notification, $table);
    }

    /**
     *  test notification
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function pushNotificationTest(Request $request)
    {
        if (isset($request['table']) && in_array($request['table'], ['reservation', 'review'])) {
            $notification = Notification::where('table', $request['table'])->first();
        } else {
            $notification = Notification::first();
        }

        if ($notification && $request['user_id']) {
            $tokens = (new TokenRepository())->getTokensByUserId($request['user_id']);
            $this->push_notification($request['title'] ?? 'aaa', $request['message'] ?? 'bbbbb', $tokens, $notification->url, $notification, 'review');
            return response()->json('notifications sent successfully');
        }

        return response()->json('there is no notifications in the database');
    }
}
