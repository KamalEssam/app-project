<?php

namespace App\Console\Commands;

use App\Http\Controllers\ApiController;
use App\Http\Repositories\Api\ReservationRepository;
use App\Models\Reservation;
use Illuminate\Console\Command;

class UpdateReservationStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservation:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update not attended reservations to missed reservations if 1 day passed on the reservation';

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
        // get all the reservation that the appoint is passed and set it as missed
        $reservations = Reservation::where('status', ApiController::STATUS_APPROVED)
            ->where('day', '<', now('Africa/Cairo')->format('Y-m-d'))
            ->get();

        foreach ($reservations as $reservation) {
            (new ReservationRepository())->updateReservaionColumn($reservation->id, 'status', ApiController::STATUS_MISSED);
        }

        echo "<=  reservations updated successfully  =>\n";
    }
}
