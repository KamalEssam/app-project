<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface NotificationInterface
{
    /**
     *  get unread notifications
     *
     * @param $user
     * @return mixed
     */
    public function getUnReadNotification($user);

    /**
     *  get list of user notifications using offset and using multicast
     *
     * @param $user
     * @param $multicast
     * @param $offset
     * @return mixed
     */
    public function getUserNotificationsUsingOffset($user, $multicast, $offset);

    /**
     *  Create New Notification
     *
     * @param $data
     * @return mixed
     */
    public function createNewNotification($data);


    /**
     *  get Notification By Id
     *
     * @param $notification_id
     * @return bool
     */
    public function getNotificationById($notification_id);

    public function canceledReservationsOnHoliday($day);
}