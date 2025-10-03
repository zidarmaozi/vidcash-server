<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Video;
use App\Models\TelegramBroadcastVideo;
use App\Services\TelegramService;

class SendVideoToTelegram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:send-video {--chat-id= : Telegram chat ID to send the video to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send one unbroadcasted video to Telegram channel/group';

    protected $telegram;

    /**
     * Create a new command instance.
     */
    public function __construct(TelegramService $telegram)
    {
        parent::__construct();
        $this->telegram = $telegram;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get chat ID from option or env
        $chatId = $this->option('chat-id') ?: env('TELEGRAM_CHAT_ID');
        
        if (!$chatId) {
            $this->error('❌ Chat ID is required! Please provide --chat-id option or set TELEGRAM_CHAT_ID in .env');
            $this->info('Usage: php artisan telegram:send-video --chat-id=YOUR_CHAT_ID');
            return Command::FAILURE;
        }

        $this->info('🔍 Looking for video to broadcast...');

        // Find video that hasn't been broadcasted yet
        $video = Video::whereDoesntHave('telegramBroadcast')
            ->where('is_active', true)
            ->where('is_safe_content', true)
            ->whereNotNull('thumbnail_path')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$video) {
            $this->warn('⚠️  No video available to broadcast.');
            $this->info('Criteria: is_active=true, is_safe_content=true, has thumbnail, not broadcasted yet');
            return Command::SUCCESS;
        }

        $this->info("📹 Found video: {$video->title}");
        $this->info("🔗 Video code: {$video->video_code}");
        $this->info("👤 Owner: {$video->user->name}");

        // Prepare message
        $message = "🎬 *{$video->title}*\n\n";
        $message .= "🔗 Watch here: {$video->generated_link}";

        $this->info("\n📤 Sending to Telegram...");

        // Send to Telegram (text only)
        $result = $this->telegram->sendMessage(
            $chatId,
            $message,
            ['parse_mode' => 'Markdown']
        );

        if (!$result['success']) {
            $this->error('❌ Failed to send video to Telegram!');
            $this->error('Error: ' . ($result['error'] ?? 'Unknown error'));
            return Command::FAILURE;
        }

        $this->info('✅ Video sent successfully to Telegram!');

        // Mark as broadcasted
        TelegramBroadcastVideo::markAsBroadcasted($video->id);
        $this->info('✅ Video marked as broadcasted in database');

        // Show summary
        $this->newLine();
        $this->info('📊 Summary:');
        $this->table(
            ['Field', 'Value'],
            [
                ['Video ID', $video->id],
                ['Video Title', $video->title],
                ['Video Code', $video->video_code],
                ['Owner', $video->user->name],
                ['Generated Link', $video->generated_link],
                ['Chat ID', $chatId],
                ['Status', '✅ Broadcasted'],
            ]
        );

        return Command::SUCCESS;
    }
}
