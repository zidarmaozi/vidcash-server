<?php

namespace App\Console\Commands;

use App\Models\Notification;
use Illuminate\Console\Command;
use Carbon\Carbon;

class PruneReadNotifications extends Command
{
    protected $signature = 'app:prune-read-notifications';
    protected $description = 'Delete read notifications older than 7 days';

    public function handle()
    {
        // Hapus notifikasi yang sudah dibaca dan lebih tua dari 7 hari
        Notification::whereNotNull('read_at')
                    ->where('created_at', '<=', Carbon::now()->subDays(7))
                    ->delete();
        
        $this->info('Old read notifications have been pruned.');
    }
}