<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TelegramBroadcastVideo;
use Carbon\Carbon;

class CleanupOldBroadcasts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:cleanup-broadcasts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove broadcast records older than 1.5 months (45 days)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Looking for broadcasts older than 45 days (1.5 months)...');

        // Calculate cutoff date (45 days = 1.5 months)
        $cutoffDate = Carbon::now()->subDays(45);
        
        // Count old broadcasts
        $count = TelegramBroadcastVideo::where('created_at', '<', $cutoffDate)->count();
        
        if ($count === 0) {
            $this->info('✅ No old broadcasts found. Database is clean!');
            return Command::SUCCESS;
        }

        $this->info("⚠️  Found {$count} broadcast(s) older than 45 days");
        $this->info("📅 Cutoff date: {$cutoffDate->format('M d, Y')}");

        // Delete old broadcasts
        $this->info("\n🗑️  Deleting old broadcasts...");
        
        $deleted = TelegramBroadcastVideo::where('created_at', '<', $cutoffDate)->delete();
        
        $this->info("✅ Successfully deleted {$deleted} broadcast record(s)");

        return Command::SUCCESS;
    }
}
