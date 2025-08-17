<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TestLeaderboard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-leaderboard';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the leaderboard query to ensure it returns correct data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing Leaderboard Query...');
        $this->newLine();

        try {
            // Test the exact query from LeaderboardController
            $topUsers = User::select(
                'users.name', 
                DB::raw('COUNT(views.id) as total_views'),
                DB::raw('SUM(CASE WHEN views.income_generated = 1 THEN views.income_amount ELSE 0 END) as total_earnings')
            )
                ->join('videos', 'users.id', '=', 'videos.user_id')
                ->join('views', 'videos.id', '=', 'views.video_id')
                ->whereMonth('views.created_at', now()->month)
                ->whereYear('views.created_at', now()->year)
                ->groupBy('users.id', 'users.name')
                ->orderBy('total_earnings', 'desc')
                ->limit(10)
                ->get();

            $this->info("✅ Query executed successfully!");
            $this->info("📊 Found {$topUsers->count()} users");
            $this->newLine();

            if ($topUsers->count() > 0) {
                $this->info("Top Users Data:");
                $this->table(
                    ['Rank', 'Name', 'Total Views', 'Total Earnings'],
                    $topUsers->map(function ($user, $index) {
                        return [
                            $index + 1,
                            $user->name,
                            number_format($user->total_views),
                            'Rp' . number_format($user->total_earnings, 0, ',', '.')
                        ];
                    })
                );
            } else {
                $this->warn("⚠️  No users found for current month");
                $this->info("This might be normal if there are no views this month");
            }

        } catch (\Exception $e) {
            $this->error("❌ Query failed: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }

        $this->info('✅ Leaderboard test completed successfully!');
        return 0;
    }
}
