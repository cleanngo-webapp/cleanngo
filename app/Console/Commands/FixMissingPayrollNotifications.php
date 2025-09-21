<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;

class FixMissingPayrollNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payroll:fix-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix missing payroll notifications for completed and paid bookings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for missing payroll notifications...');
        
        $count = Booking::triggerMissingPayrollNotifications();
        
        $this->info("Triggered payroll notifications for {$count} bookings.");
        
        return Command::SUCCESS;
    }
}
