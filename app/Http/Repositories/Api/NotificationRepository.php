<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Api;


use App\Http\Interfaces\Api\NotificationInterface;
use App\Models\Notification;
use Carbon\Carbon;

class NotificationRepository implements NotificationInterface
{
    protected $notification;

    public function __construct()
    {
        $this->notification = new Notification();
    }

    /**
     *  get list of notifications
     *
     * @param $receiver_id
     * @param $offset
     * @param $limit
     * @return mixed
     */
    public function getUserListOfNotifications($receiver_id, $offset, $limit)
    {
        try {
            $notifications = $this->notification->where('receiver_id', $receiver_id)
                ->orderBy('created_at', 'desc')
                ->offset($offset)
                ->limit($limit)
                ->select('id', app()->getLocale() . '_title as title', app()->getLocale() . '_message as message', 'table as click_action', 'is_read', 'created_at');
            return $notifications;

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     *  get count of unread notifications
     *
     * @param $receiver_id
     * @return mixed
     */
    public function getUserNotificationsCount($receiver_id)
    {
        try {
            return $this->notification->where('receiver_id', $receiver_id)
                ->where('is_read', 0)
                ->count();
        } catch (\Exception $e) {
            return false;
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
        return Notification::create($data);
    }

    /**
     * @param $multicast
     * @param $receiver
     * @param $offset
     * @param $limit
     * @return mixed
     */
    public function getAdminNotification($multicast, $receiver, $offset, $limit)
    {
        try {
            $notifications = $this->notification
                ->where('multicast', $multicast)
                ->where('receiver_id', $receiver)
                ->orderBy('created_at', 'desc')
                ->offset($offset)
                ->limit($limit)
                ->select('id', app()->getLocale() . '_title as title', app()->getLocale() . '_message as message', 'table as click_action', 'is_read', 'created_at');
            return $notifications;

        } catch (\Exception $e) {
            return false;
        }
    }
}
