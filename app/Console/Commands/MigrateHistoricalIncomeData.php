<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\View;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class MigrateHistoricalIncomeData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-historical-income-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate historical view data to include income tracking information';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting historical income data migration...');

        // Get current CPM setting
        $currentCpm = (int) (Setting::where('key', 'cpm')->first()->value ?? 10);
        $this->info("Current CPM setting: Rp{$currentCpm}");

        // Count total views that need migration
        $totalViews = View::count();
        $this->info("Total views to migrate: {$totalViews}");

        if ($totalViews === 0) {
            $this->info('No views found to migrate.');
            return 0;
        }

        // Start migration
        $this->info('Migrating historical data...');
        
        $bar = $this->output->createProgressBar($totalViews);
        $bar->start();

        // Process in chunks to avoid memory issues
        View::chunk(1000, function ($views) use ($currentCpm, $bar) {
            foreach ($views as $view) {
                // For historical data, we assume all views passed validation and generated income
                // This is a reasonable assumption for existing data
                $view->update([
                    'income_amount' => $currentCpm, // 1 view = CPM amount
                    'cpm_at_time' => $currentCpm,   // Use current CPM as historical CPM
                    'validation_passed' => true,    // Assume passed for historical data
                    'income_generated' => true,     // Assume generated income for historical data
                ]);
                
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();

        // Verify migration
        $this->info('Verifying migration...');
        
        $migratedViews = View::whereNotNull('income_amount')->count();
        $totalIncome = View::where('income_generated', true)->sum('income_amount');
        
        $this->info("Views with income data: {$migratedViews}");
        $this->info("Total income calculated: Rp" . number_format($totalIncome, 0, ',', '.'));
        
        if ($migratedViews === $totalViews) {
            $this->info('✅ Migration completed successfully!');
        } else {
            $this->warn('⚠️  Some views may not have been migrated properly.');
        }

        $this->info('Historical income data migration completed.');
        return 0;
    }
}
