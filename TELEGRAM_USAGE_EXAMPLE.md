# Telegram Service - Cara Penggunaan

Service ini memungkinkan Anda mengirim pesan Telegram dengan gambar + teks dalam satu pesan.

## Setup

### 1. Tambahkan Bot Token ke .env
```env
TELEGRAM_BOT_TOKEN=your_bot_token_here
```

### 2. Cara Mendapatkan Bot Token
1. Buka Telegram dan cari `@BotFather`
2. Kirim command `/newbot`
3. Ikuti instruksi untuk membuat bot baru
4. Copy bot token yang diberikan

### 3. Cara Mendapatkan Chat ID
1. Untuk personal chat: 
   - Kirim pesan ke bot Anda
   - Buka browser dan akses: `https://api.telegram.org/bot<YOUR_BOT_TOKEN>/getUpdates`
   - Cari `"chat":{"id":` untuk mendapatkan chat ID

2. Untuk channel/group:
   - Tambahkan bot ke channel/group
   - Chat ID biasanya dimulai dengan `-` (negatif)

## Contoh Penggunaan

### 1. Mengirim Gambar dengan Caption (dari URL)

```php
use App\Services\TelegramService;

class ExampleController extends Controller
{
    public function sendNotification()
    {
        $telegram = new TelegramService();
        
        $chatId = '123456789'; // Ganti dengan chat ID Anda
        $imageUrl = 'https://example.com/image.jpg';
        $caption = "Halo! Ini adalah gambar dengan caption.\n\nBaris kedua caption.";
        
        $result = $telegram->sendPhotoWithCaption($chatId, $imageUrl, $caption);
        
        if ($result['success']) {
            return response()->json(['message' => 'Pesan berhasil dikirim']);
        }
        
        return response()->json(['error' => $result['message']], 400);
    }
}
```

### 2. Mengirim Gambar dari File Lokal

```php
use App\Services\TelegramService;

class ExampleController extends Controller
{
    public function sendLocalImage()
    {
        $telegram = new TelegramService();
        
        $chatId = '123456789';
        $imagePath = storage_path('app/public/images/example.jpg');
        $caption = "Gambar ini dikirim dari file lokal";
        
        $result = $telegram->sendPhotoFromFile($chatId, $imagePath, $caption);
        
        return response()->json($result);
    }
}
```

### 3. Mengirim dengan Format HTML atau Markdown

```php
use App\Services\TelegramService;

$telegram = new TelegramService();

// Dengan HTML
$caption = "<b>Teks Tebal</b>\n<i>Teks Miring</i>\n<u>Teks Garis Bawah</u>";
$options = [
    'parse_mode' => 'HTML'
];

$telegram->sendPhotoWithCaption($chatId, $imageUrl, $caption, $options);

// Dengan Markdown
$caption = "*Teks Tebal*\n_Teks Miring_\n`Kode`";
$options = [
    'parse_mode' => 'Markdown'
];

$telegram->sendPhotoWithCaption($chatId, $imageUrl, $caption, $options);
```

### 4. Mengirim Tanpa Notifikasi (Silent)

```php
use App\Services\TelegramService;

$telegram = new TelegramService();

$options = [
    'disable_notification' => true
];

$telegram->sendPhotoWithCaption($chatId, $imageUrl, $caption, $options);
```

### 5. Mengirim Pesan Teks Biasa

```php
use App\Services\TelegramService;

$telegram = new TelegramService();

$text = "Halo! Ini adalah pesan teks biasa.";
$result = $telegram->sendMessage($chatId, $text);
```

### 6. Mengirim Document dengan Caption

```php
use App\Services\TelegramService;

$telegram = new TelegramService();

$documentUrl = 'https://example.com/document.pdf';
$caption = "Ini adalah document PDF";
$result = $telegram->sendDocument($chatId, $documentUrl, $caption);
```

### 7. Mengecek Info Bot

```php
use App\Services\TelegramService;

$telegram = new TelegramService();

$result = $telegram->getBotInfo();
if ($result['success']) {
    $botInfo = $result['data']['result'];
    echo "Bot Name: " . $botInfo['first_name'];
    echo "Bot Username: @" . $botInfo['username'];
}
```

## Contoh Penggunaan di Controller

```php
<?php

namespace App\Http\Controllers;

use App\Services\TelegramService;
use Illuminate\Http\Request;

class TelegramNotificationController extends Controller
{
    protected $telegram;
    
    public function __construct(TelegramService $telegram)
    {
        $this->telegram = $telegram;
    }
    
    /**
     * Kirim notifikasi video baru ke Telegram
     */
    public function notifyNewVideo(Request $request)
    {
        $chatId = env('TELEGRAM_ADMIN_CHAT_ID'); // ID chat admin
        
        $imageUrl = $request->input('thumbnail_url');
        $caption = "🎬 *Video Baru Diupload!*\n\n";
        $caption .= "📝 Judul: " . $request->input('title') . "\n";
        $caption .= "👤 Uploader: " . $request->input('uploader_name') . "\n";
        $caption .= "🔗 Link: " . $request->input('video_url');
        
        $options = [
            'parse_mode' => 'Markdown',
            'disable_notification' => false
        ];
        
        $result = $this->telegram->sendPhotoWithCaption($chatId, $imageUrl, $caption, $options);
        
        return response()->json($result);
    }
    
    /**
     * Kirim report video ke Telegram
     */
    public function notifyVideoReport(Request $request)
    {
        $chatId = env('TELEGRAM_ADMIN_CHAT_ID');
        
        $imageUrl = $request->input('video_thumbnail');
        $caption = "⚠️ *VIDEO REPORTED*\n\n";
        $caption .= "📝 Video: " . $request->input('video_title') . "\n";
        $caption .= "💬 Alasan: " . $request->input('report_reason') . "\n";
        $caption .= "🌐 IP Reporter: " . $request->ip();
        
        $options = [
            'parse_mode' => 'Markdown'
        ];
        
        $result = $this->telegram->sendPhotoWithCaption($chatId, $imageUrl, $caption, $options);
        
        return response()->json($result);
    }
}
```

## Opsi Tambahan yang Tersedia

Anda bisa menambahkan opsi berikut ke parameter `$options`:

- `parse_mode`: 'HTML', 'Markdown', atau 'MarkdownV2'
- `disable_notification`: `true` untuk silent notification
- `protect_content`: `true` untuk mencegah forward/save
- `reply_to_message_id`: ID pesan untuk reply
- `disable_web_page_preview`: `true` untuk disable preview link (hanya untuk sendMessage)

## Error Handling

Service ini mengembalikan array dengan struktur:
```php
[
    'success' => true/false,
    'data' => [...], // jika success
    'error' => [...], // jika failed
    'message' => 'Pesan status'
]
```

Contoh penanganan error:
```php
$result = $telegram->sendPhotoWithCaption($chatId, $imageUrl, $caption);

if (!$result['success']) {
    Log::error('Gagal kirim Telegram: ' . $result['message']);
    // Lakukan sesuatu jika gagal
}
```

## Tips

1. **Caption Max Length**: Caption maksimal 1024 karakter
2. **Image Size**: Maksimal 10MB untuk photo, gunakan sendDocument untuk file lebih besar
3. **Rate Limit**: Telegram membatasi 30 pesan/detik per chat
4. **Testing**: Gunakan getUpdates untuk debug: `https://api.telegram.org/bot<TOKEN>/getUpdates`

