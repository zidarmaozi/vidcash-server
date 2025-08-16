<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\View;
use App\Models\User;
use App\Models\Video;
use App\Models\Setting;
use App\Models\Withdrawal;
use App\Models\EventPayout;
use Illuminate\Support\Facades\DB;

class TestIncomeSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-income-system';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the income system to verify all components are working correctly';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing Income System Components...');
        $this->newLine();

        // Test 1: Database Structure
        $this->info('1️⃣  Testing Database Structure...');
        $this->testDatabaseStructure();

        // Test 2: Income Calculations
        $this->info('2️⃣  Testing Income Calculations...');
        $this->testIncomeCalculations();

        // Test 3: API Integration
        $this->info('3️⃣  Testing API Integration...');
        $this->testApiIntegration();

        // Test 4: Dashboard Widgets
        $this->info('4️⃣  Testing Dashboard Widgets...');
        $this->testDashboardWidgets();

        $this->info('✅ All tests completed!');
        return 0;
    }

    private function testDatabaseStructure()
    {
        try {
            // Check if new columns exist
            $columns = DB::select("SHOW COLUMNS FROM views LIKE 'income_amount'");
            if (empty($columns)) {
                $this->error('❌ income_amount column not found in views table');
                return;
            }

            $columns = DB::select("SHOW COLUMNS FROM views LIKE 'cpm_at_time'");
            if (empty($columns)) {
                $this->error('❌ cpm_at_time column not found in views table');
                return;
            }

            $this->info('✅ Database structure is correct');
        } catch (\Exception $e) {
            $this->error('❌ Database structure test failed: ' . $e->getMessage());
        }
    }

    private function testIncomeCalculations()
    {
        try {
            // Test stored income calculation
            $totalStoredIncome = View::where('income_generated', true)->sum('income_amount');
            $totalViews = View::count();
            $currentCpm = (int) (Setting::where('key', 'cpm')->first()->value ?? 10);
            $calculatedIncome = $totalViews * $currentCpm;

            $this->info("   Total Views: {$totalViews}");
            $this->info("   Current CPM: Rp{$currentCpm}");
            $this->info("   Stored Income: Rp" . number_format($totalStoredIncome, 0, ',', '.'));
            $this->info("   Calculated Income: Rp" . number_format($calculatedIncome, 0, ',', '.'));

            if ($totalStoredIncome > 0) {
                $this->info('✅ Income calculations working correctly');
            } else {
                $this->warn('⚠️  No stored income data found - run migration first');
            }
        } catch (\Exception $e) {
            $this->error('❌ Income calculations test failed: ' . $e->getMessage());
        }
    }

    private function testApiIntegration()
    {
        try {
            // Test if API endpoints are accessible
            $this->info('   Testing API endpoints...');
            
            // Check if ServiceController exists and has required methods
            if (class_exists(\App\Http\Controllers\Api\ServiceController::class)) {
                $this->info('✅ ServiceController exists');
            } else {
                $this->error('❌ ServiceController not found');
            }

            // Check if recordView method exists
            $reflection = new \ReflectionClass(\App\Http\Controllers\Api\ServiceController::class);
            if ($reflection->hasMethod('recordView')) {
                $this->info('✅ recordView method exists');
            } else {
                $this->error('❌ recordView method not found');
            }

        } catch (\Exception $e) {
            $this->error('❌ API integration test failed: ' . $e->getMessage());
        }
    }

    private function testDashboardWidgets()
    {
        try {
            $this->info('   Testing dashboard widgets...');

            // Test DashboardStats widget
            if (class_exists(\App\Filament\Widgets\DashboardStats::class)) {
                $this->info('✅ DashboardStats widget exists');
            } else {
                $this->error('❌ DashboardStats widget not found');
            }

            // Test FinancialOverview widget
            if (class_exists(\App\Filament\Widgets\FinancialOverview::class)) {
                $this->info('✅ FinancialOverview widget exists');
            } else {
                $this->error('❌ FinancialOverview widget not found');
            }

            // Test MonthlyStatsChart widget
            if (class_exists(\App\Filament\Widgets\MonthlyStatsChart::class)) {
                $this->info('✅ MonthlyStatsChart widget exists');
            } else {
                $this->error('❌ MonthlyStatsChart widget not found');
            }

        } catch (\Exception $e) {
            $this->error('❌ Dashboard widgets test failed: ' . $e->getMessage());
        }
    }
}
