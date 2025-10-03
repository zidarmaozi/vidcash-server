<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;

class SendAdvertiseToTelegram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:send-advertise';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Vidcash advertise to Telegram channel with image';

    private $telegram;

    public function __construct()
    {
        parent::__construct();
        $this->telegram = new TelegramService();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Vidcash Advertise Sender');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        // Get chat ID from config
        $chatId = config('services.telegram.chat_id');
        
        if (!$chatId) {
            $this->error('❌ Chat ID is required!');
            $this->info('💡 Please set TELEGRAM_CHAT_ID in .env');
            return Command::FAILURE;
        }

        $this->info("📢 Target channel: {$chatId}");

        // Image stored in public/advertise.jpg
        $imagePath = public_path('advertise.jpg');

        // Check if file exists
        if (!file_exists($imagePath)) {
            $this->error("❌ Image file not found: {$imagePath}");
            $this->warn('💡 Please update the $imagePath in the handle() method');
            return Command::FAILURE;
        }

        // Prepare advertise message
        $message = $this->getAdvertiseMessage();

        $this->newLine();
        $this->info('📝 Advertise Message Preview:');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line($message);
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();

        $this->info("📸 Image path: {$imagePath}");
        $this->info('📊 Image size: ' . $this->formatBytes(filesize($imagePath)));
        $this->newLine();

        // Send to Telegram
        $this->info('📤 Sending advertise to Telegram...');

        $result = $this->telegram->sendPhotoFromFile($chatId, $imagePath, $message, [
            'parse_mode' => 'Markdown',
        ]);

        if ($result['success']) {
            $this->info('✅ Advertise sent successfully!');
            $this->info('🎉 Your advertise has been posted to the channel!');
            return Command::SUCCESS;
        }

        $this->error('❌ Failed to send advertise!');
        $this->error('Error: ' . json_encode($result['error'] ?? 'Unknown error'));
        return Command::FAILURE;
    }

    /**
     * Get advertise message template
     */
    private function getAdvertiseMessage(): string
    {
        return "🎬 *VIDCASH - Upload Video, Dapat Cuan!* 🤑

💰 *Streaming Sambil Cari Uang?*
Sekarang bisa banget di Vidcash! Platform streaming pertama di Indonesia yang bayar kamu untuk nonton video!

✨ *Kenapa Harus Vidcash?*
• 🎥 Upload video berkualitas
• 💸 Dapat penghasilan REAL
• ⚡ Penarikan mudah & cepat
• 🔒 Aman & terpercaya
• 📱 Bisa di HP, tablet, atau PC

🚀 *Cara Kerja:*
1️⃣ Daftar GRATIS di Vidcash
2️⃣ Upload video kamu atau generate link
3️⃣ Share link ke teman-teman
4️⃣ Setiap orang nonton = kamu DAPAT CUAN! 💵

💎 *Cocok untuk:*
✅ Content creator
✅ Influencer
✅ Yang mau passive income
✅ Mahasiswa cari uang saku
✅ Siapa aja yang mau cuan!

🔥 *Join Sekarang & Mulai Hasilkan Uang!*
🌐 **vidcash.cc**

📢 Jangan sampai ketinggalan kesempatan emas ini!
👥 Ribuan user sudah join & mulai earning!

#Vidcash #StreamingDanEarn #PassiveIncome #ContentCreator #Indonesia";
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
