<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class SyncBookingsCheckoutStatusCommand extends Command
{
    protected $signature = 'bookings:sync-checkout-status';

    protected $description = 'Moves finished confirmed bookings to check_out status';

    public function handle(): int
    {
        $updated = Booking::syncExpiredConfirmedToCheckout();

        $this->info("Bookings moved to check_out: {$updated}");

        return self::SUCCESS;
    }
}
