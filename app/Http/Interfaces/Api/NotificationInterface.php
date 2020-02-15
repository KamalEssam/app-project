<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Api;


interface NotificationInterface
{
    /**
     *  get list of notifications
     *
     * @param $receiver_id
     * @param $offset
     * @param $limit
     * @return mixed
     */
    public function getUserListOfNotifications($receiver_id, $offset, $limit);

    /**
     *  get count of unread notifications
     *
     * @param $receiver_id
     * @return mixed
     */
    public function getUserNotificationsCount($receiver_id);

    /**
     *  Create New Notification
     *
     * @param $data
     * @return mixed
     */
    public function createNewNotification($data);

    /**
     * @param $multicast
     * @param $clinic_id
     * @param $offset
     * @param $limit
     * @return mixed
     */
    public function getAdminNotification($multicast, $clinic_id, $offset, $limit);
}