<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Video;
use Illuminate\Support\Facades\DB;

class UpdateVideosIsActiveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update semua video yang sudah ada untuk memiliki is_active = true
        Video::query()->update(['is_active' => true]);
        
        $this->command->info('All existing videos have been set to active status.');
    }
}
