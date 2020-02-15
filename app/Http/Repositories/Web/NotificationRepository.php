<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;

use App\Http\Controllers\WebController;
use App\Http\Interfaces\Web\NotificationInterface;
use App\Http\Traits\NotificationTrait;
use App\Http\Traits\SmsTrait;
use App\Models\Notification;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Collection;

class NotificationRepository extends ParentRepository implements NotificationInterface
{
    public $notification;
    use NotificationTrait, SmsTrait;

    public function __construct()
    {
        $this->notification = new Notification();
    }

    /**
     *  get unread notifications
     *
     * @param $user
     * @return mixed
     */
    public function getUnReadNotification($user)
    {
        try {
            return $this->notification->join('reservations', function ($join) {
                $join->on('reservations.id', '=', 'notifications.object_id');
                $join->where('notifications.table', '=', 'reservations');

            })
                ->where(function ($query) use ($user) {
                    // in case of just user
                    $query->where(function ($query) use ($user) {
                        $query->where('notifications.multicast', 0);
                        $query->where('notifications.receiver_id', $user->id);
                    });

                    if ($user->role_id == 2) {
                        // in case of assistant
                        $query->orWhere(function ($query) use ($user) {
                            $query->where('notifications.multicast', $user->role_id);
                            $query->where('notifications.receiver_id', $user->clinic_id);
                        });

                    } else if ($user->role_id == 1) {
                        // in case of doctor
                        $query->orWhere(function ($query) use ($user) {
                            $query->where('notifications.multicast', $user->role_id);
                            $query->where('notifications.receiver_id', $user->id);
                        });
                    }
                })
                ->where(function ($query) use ($user) {
                    if ($user->last_notification_click != null) {
                        $query->where('notifications.created_at', '>', $user->last_notification_click);
                    }
                })
                ->where('notifications.is_read', 0)
                ->select('notifications.*')
                ->get();

        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return new Collection();
        }
    }

    /**
     *  get list of user notifications using offset and using multicast
     *
     * @param $user
     * @param $multicast
     * @param $offset
     * @return mixed
     */
    public function getUserNotificationsUsingOffset($user, $multicast, $offset)
    {
        try {
            return $this->notification->join('reservations', function ($join) {
                $join->on('reservations.id', '=', 'notifications.object_id');
                $join->where('notifications.table', '=', 'reservations');

            })
                ->where(function ($query) use ($user) {
                    // in case of just user
                    $query->where(function ($query) use ($user) {
                        $query->where('notifications.multicast', 0);
                        $query->where('notifications.receiver_id', $user->id);
                    });

                    if ($user->role_id == 2) {
                        // in case of assistant
                        $query->orWhere(function ($query) use ($user) {
                            $query->where('notifications.multicast', $user->role_id);
                            $query->where('notifications.receiver_id', $user->clinic_id);
                        });

                    } else if ($user->role_id == 1) {
                        // in case of doctor
                        $query->orWhere(function ($query) use ($user) {
                            $query->where('notifications.multicast', $user->role_id);
                            $query->where('notifications.receiver_id', $user->account_id);

                        });
                    }
                })
                ->select('notifications.*')
                ->orderBy('notifications.created_at', 'desc')
                ->offset($offset)
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return new Collection();
        }
    }

    /**
     *  Create New Notification
     *
     * @param $data
     * @return mixed
     */
    public function createNewNotification($data)
    {
        try {
            return $this->notification->create($data);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get Notification By Id
     *
     * @param $notification_id
     * @return bool
     */
    public function getNotificationById($notification_id)
    {
        try {
            $notification = $this->notification->find($notification_id);
            $notification->is_read = 1;
            $notification->update();
            if (!$notification) {
                return false;
            }
            return $notification;
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * @param $day
     */
    public function canceledReservationsOnHoliday($day)
    {

        $cancel_reservations = Reservation::whereIn('status', [WebController::R_STATUS_APPROVED])
            ->where('day', $day)->get();

        foreach ($cancel_reservations as $cancel_reservation) {
            $cancel_reservation->status = WebController::R_STATUS_CANCELED;
            $cancel_reservation->update();

            $ar_msg = 'لقد تم الغاء حجزك اليوم لان هذا اليوم اجازه';
            $en_msg = 'your reservation has been canceled because this day is holiday';

            // create notification to be pushed to patient
            $notification = Notification::create([
                'multicast' => 0,
                'sender_id' => auth()->user()->id,
                'receiver_id' => $cancel_reservation->user_id,
                'en_title' => auth()->user()->account['en_name'],
                'ar_title' => auth()->user()->account['ar_name'],
                'en_message' => $en_msg,
                'ar_message' => $ar_msg,
                'url' => 'reservations',
                'object_id' => $cancel_reservation->id,
                'table' => 'reservations',
            ]);

            $tokens = (new TokenRepository())->getTokensByUserId($notification->receiver_id);
            $this->push_notification($notification[app()->getLocale() . '_title'], $notification[app()->getLocale() . '_message'], $tokens, $notification->url, $notification);

            // send sms to patients
            if (app()->getLocale() == 'en') {
                $sms_msg = $en_msg;
                $lang = 1;
            } else {
                $sms_msg = $ar_msg;
                $lang = 2;
            }
            $mobile = $cancel_reservation->user->mobile;
            try {
                self::sendRklinicSmsMessage($mobile, $sms_msg, $lang);
            } catch (\Exception $e) {
                \Log::error($e->getMessage());
            }
        }
    }

    /**
     * @param int $type
     * @param string $notification
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNotificationListWeb($type = 0, $notification = '')
    {
        $user = auth()->user();

        try {
            $notifications = $this->notification

                ->where(function ($query) use ($user) {
                    // in case of just user
                    $query->where(function ($query) use ($user) {
                        $query->where('notifications.multicast', 0);
                        $query->where('notifications.receiver_id', $user->id);
                    });

                    if ($user->role_id == 2) {
                        // in case of assistant
                        $query->orWhere(function ($query) use ($user) {
                            $query->where('notifications.multicast', $user->role_id);
                            $query->where('notifications.receiver_id', $user->clinic_id);
                        });

                    } else if ($user->role_id == 1) {
                        // in case of doctor
                        $query->orWhere(function ($query) use ($user) {
                            $query->where('notifications.multicast', $user->role_id);
                            $query->where('notifications.receiver_id', $user->id);
                        });
                    }
                })
                ->where(function ($query) use ($type, $notification) {
                    if ($type == 0) { // count of notifications
                        $query->where('notifications.is_read', 0);
                    }
                    if ($notification != '') {
                        $query->where('notifications.id', $notification);
                    }
                })
                ->select('notifications.*')
                ->orderBy('notifications.created_at', 'desc')
                ->get();

        } catch (\Exception $e) {
            return response()->json(['status' => false], 500);
        }
        if ($type == 0) {
            return $notifications->count();
        }

        return $notifications;
    }
}
