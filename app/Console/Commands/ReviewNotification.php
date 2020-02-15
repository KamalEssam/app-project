<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\ApiController;
use App\Models\Clinic;
use App\Models\Notification;
use App\Models\Reservation;
use App\Models\Review;
use Illuminate\Console\Command;

class ReviewNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reviews:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Notifications to Users';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // loop all the reservations in the application and send notification to the one that is not reviewed
        $reservations = Reservation::whereIn('status', [ApiController::STATUS_MISSED, ApiController::STATUS_ATTENDED])
            ->where('reservation_ignore', 0)
            ->whereNotIn('id',
                Review::select('reservation_id')
                    ->get()
                    ->toArray() // Reservation don't have review
            )
            ->whereNotIn('id',
                \DB::table('notifications')
                    ->select('object_id')
                    ->where('table', 'review')
                    ->pluck('object_id')  // we did not send notification before
            )
            ->get();

        if (count($reservations) > 0) {
            // send the notifications
            foreach ($reservations as $reservation) {

                $account = Clinic::with('account')->where('id', $reservation->clinic_id)->first();

                if ($account) {
                    (new NotificationController())->sendNotificationToReviewReservation(
                        $reservation->user_id,
                        'تقييم الزيارة',
                        'Review Visit ',
                        'How was your visit with ' . $account->account->en_name ?? 'last reservation ' . ', review now?',
                        'كيف كانت الزيارة مع ' . $account->account->ar_name ?? ' اخر زياره ' . ' ، قم بتقييم الزيارة',
                        $reservation->id,
                        'review'
                    );
                }
            }
        }
    }
}

