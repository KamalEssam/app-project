<?php


namespace App\Http\Traits;

use App\Models\Notification;

trait NotificationTrait
{
    /**
     * @param $title
     * @param $body
     * @param $tokens
     * @param $action
     * @param $notification
     * @param string $table
     * @return bool|string
     */
    public function push_notification($title, $body, $tokens, $action, $notification, $table = 'reservation')
    {
        #prep the bundle
        $notification_object = array
        (
            'title' => $title,
            'body' => $body,
            'click_action' => $table,   // will be reservation or review
            'notification' => $notification,
            'sound' => 'default',
            'icon' => 'assets/images/logo/logo-125.png',
            'object_id' => $notification->object_id,
            'url' => $action   // url
        );

        $data = array
        (
            'title' => $title,
            'body' => $body,
            'click_action' => $table,
            'notification' => $notification,
            'sound' => 'default',
            'icon' => 'assets/images/logo/logo-125.png',
            'object_id' => $notification->object_id,
            'url' => $action  // url
        );

        $fields = array
        (

            'registration_ids' => $tokens,
            'notification' => $notification_object,
            'data' => $data,

        );

        $headers = array
        (
            'Authorization: key=AAAAFQdZjw4:APA91bEkv0Xm6IGZf02kYPhg2nBlFmf3kLFIfrHrTipSq92dJJnf08JZ_R-RPF6BhjlTXz5SdCRKMdhPPACgshZz5tEvK7z7PKIHytvip7TH2pttw8fURkDi9T03O4Ex050Ez-TnV23Z',
            'Content-Type: application/json'
        );


        #Send Reponse To FireBase Server
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     *  set notification read when enter reservation pages [pending, confirmed]
     *
     * @param $reservation_status
     */
    public function setNotificationAsRead($reservation_status)
    {
        $notifications = Notification::join('reservations', 'notifications.object_id', '=', 'reservations.id')
            ->where('reservations.status', $reservation_status)
            ->where('notifications.receiver_id', auth()->user()->id)
            ->where('notifications.is_read', 0)
            ->select('notifications.*')
            ->get();

        foreach ($notifications as $notification) {
            $notification->is_read = 1;
            $notification->update();
        }
    }
}
