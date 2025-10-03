<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    private $botToken;
    private $apiUrl;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}";
    }

    /**
     * Kirim pesan dengan gambar (photo) dan caption
     *
     * @param string $chatId - ID chat Telegram (bisa user ID atau channel ID)
     * @param string $imageUrl - URL gambar yang akan dikirim
     * @param string|null $caption - Teks caption untuk gambar (opsional)
     * @param array $options - Opsi tambahan (parse_mode, disable_notification, dll)
     * @return array
     */
    public function sendPhotoWithCaption($chatId, $imageUrl, $caption = null, array $options = [])
    {
        try {
            $params = [
                'chat_id' => $chatId,
                'photo' => $imageUrl,
            ];

            if ($caption) {
                $params['caption'] = $caption;
            }

            // Merge dengan opsi tambahan
            $params = array_merge($params, $options);

            $response = Http::timeout(30)->post("{$this->apiUrl}/sendPhoto", $params);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'message' => 'Pesan berhasil dikirim'
                ];
            }

            return [
                'success' => false,
                'error' => $response->json(),
                'message' => 'Gagal mengirim pesan'
            ];

        } catch (\Exception $e) {
            Log::error('Telegram Send Photo Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Terjadi kesalahan saat mengirim pesan'
            ];
        }
    }

    /**
     * Kirim pesan dengan gambar dari file lokal
     *
     * @param string $chatId - ID chat Telegram
     * @param string $imagePath - Path file gambar lokal
     * @param string|null $caption - Teks caption untuk gambar (opsional)
     * @param array $options - Opsi tambahan
     * @return array
     */
    public function sendPhotoFromFile($chatId, $imagePath, $caption = null, array $options = [])
    {
        try {
            if (!file_exists($imagePath)) {
                return [
                    'success' => false,
                    'error' => 'File tidak ditemukan',
                    'message' => 'File gambar tidak ditemukan'
                ];
            }

            $params = [
                'chat_id' => $chatId,
                'photo' => fopen($imagePath, 'r'),
            ];

            if ($caption) {
                $params['caption'] = $caption;
            }

            // Merge dengan opsi tambahan
            $params = array_merge($params, $options);

            $response = Http::timeout(30)
                ->attach('photo', file_get_contents($imagePath), basename($imagePath))
                ->post("{$this->apiUrl}/sendPhoto", array_merge($params, ['photo' => null]));

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'message' => 'Pesan berhasil dikirim'
                ];
            }

            return [
                'success' => false,
                'error' => $response->json(),
                'message' => 'Gagal mengirim pesan'
            ];

        } catch (\Exception $e) {
            Log::error('Telegram Send Photo From File Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Terjadi kesalahan saat mengirim pesan'
            ];
        }
    }

    /**
     * Kirim pesan teks biasa
     *
     * @param string $chatId - ID chat Telegram
     * @param string $text - Teks pesan
     * @param array $options - Opsi tambahan (parse_mode, disable_notification, dll)
     * @return array
     */
    public function sendMessage($chatId, $text, array $options = [])
    {
        try {
            $params = [
                'chat_id' => $chatId,
                'text' => $text,
            ];

            // Merge dengan opsi tambahan
            $params = array_merge($params, $options);

            $response = Http::timeout(30)->post("{$this->apiUrl}/sendMessage", $params);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'message' => 'Pesan berhasil dikirim'
                ];
            }

            return [
                'success' => false,
                'error' => $response->json(),
                'message' => 'Gagal mengirim pesan'
            ];

        } catch (\Exception $e) {
            Log::error('Telegram Send Message Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Terjadi kesalahan saat mengirim pesan'
            ];
        }
    }

    /**
     * Kirim document dengan caption
     *
     * @param string $chatId - ID chat Telegram
     * @param string $documentUrl - URL document yang akan dikirim
     * @param string|null $caption - Teks caption untuk document (opsional)
     * @param array $options - Opsi tambahan
     * @return array
     */
    public function sendDocument($chatId, $documentUrl, $caption = null, array $options = [])
    {
        try {
            $params = [
                'chat_id' => $chatId,
                'document' => $documentUrl,
            ];

            if ($caption) {
                $params['caption'] = $caption;
            }

            // Merge dengan opsi tambahan
            $params = array_merge($params, $options);

            $response = Http::timeout(30)->post("{$this->apiUrl}/sendDocument", $params);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'message' => 'Document berhasil dikirim'
                ];
            }

            return [
                'success' => false,
                'error' => $response->json(),
                'message' => 'Gagal mengirim document'
            ];

        } catch (\Exception $e) {
            Log::error('Telegram Send Document Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Terjadi kesalahan saat mengirim document'
            ];
        }
    }

    /**
     * Get info tentang bot
     *
     * @return array
     */
    public function getBotInfo()
    {
        try {
            $response = Http::timeout(30)->get("{$this->apiUrl}/getMe");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'message' => 'Berhasil mendapatkan info bot'
                ];
            }

            return [
                'success' => false,
                'error' => $response->json(),
                'message' => 'Gagal mendapatkan info bot'
            ];

        } catch (\Exception $e) {
            Log::error('Telegram Get Bot Info Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Terjadi kesalahan saat mendapatkan info bot'
            ];
        }
    }

    /**
     * Cek apakah user adalah member/participant dari channel/group
     *
     * @param string|int $chatId - ID channel/group (bisa string dengan @ atau numeric ID)
     * @param string|int $userId - ID user Telegram yang akan dicek
     * @return array
     */
    public function checkChannelMembership($chatId, $userId)
    {
        try {
            $response = Http::timeout(30)->get("{$this->apiUrl}/getChatMember", [
                'chat_id' => $chatId,
                'user_id' => $userId,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Status yang dianggap sebagai member aktif
                $activeMemberStatuses = ['creator', 'administrator', 'member'];
                $status = $data['result']['status'] ?? 'left';
                
                $isMember = in_array($status, $activeMemberStatuses);

                return [
                    'success' => true,
                    'is_member' => $isMember,
                    'status' => $status,
                    'data' => $data['result'],
                    'message' => $isMember ? 'User adalah member channel' : 'User bukan member channel'
                ];
            }

            return [
                'success' => false,
                'is_member' => false,
                'error' => $response->json(),
                'message' => 'Gagal mengecek keanggotaan channel'
            ];

        } catch (\Exception $e) {
            Log::error('Telegram Check Channel Membership Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'is_member' => false,
                'error' => $e->getMessage(),
                'message' => 'Terjadi kesalahan saat mengecek keanggotaan channel'
            ];
        }
    }

    /**
     * Cek apakah user adalah member dari multiple channel/group
     *
     * @param array $chatIds - Array ID channel/group yang akan dicek
     * @param string|int $userId - ID user Telegram yang akan dicek
     * @return array
     */
    public function checkMultipleChannelMembership(array $chatIds, $userId)
    {
        try {
            $results = [];
            $allMember = true;

            foreach ($chatIds as $chatId) {
                $result = $this->checkChannelMembership($chatId, $userId);
                $results[$chatId] = $result;
                
                if (!$result['is_member']) {
                    $allMember = false;
                }
            }

            return [
                'success' => true,
                'is_member_of_all' => $allMember,
                'results' => $results,
                'message' => $allMember ? 'User adalah member semua channel' : 'User bukan member beberapa channel'
            ];

        } catch (\Exception $e) {
            Log::error('Telegram Check Multiple Channel Membership Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'is_member_of_all' => false,
                'error' => $e->getMessage(),
                'message' => 'Terjadi kesalahan saat mengecek keanggotaan multiple channel'
            ];
        }
    }

    /**
     * Get informasi detail tentang chat/channel/group
     *
     * @param string|int $chatId - ID chat/channel/group
     * @return array
     */
    public function getChatInfo($chatId)
    {
        try {
            $response = Http::timeout(30)->get("{$this->apiUrl}/getChat", [
                'chat_id' => $chatId,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()['result'],
                    'message' => 'Berhasil mendapatkan info chat'
                ];
            }

            return [
                'success' => false,
                'error' => $response->json(),
                'message' => 'Gagal mendapatkan info chat'
            ];

        } catch (\Exception $e) {
            Log::error('Telegram Get Chat Info Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Terjadi kesalahan saat mendapatkan info chat'
            ];
        }
    }

    /**
     * Get jumlah member dalam channel/group
     *
     * @param string|int $chatId - ID chat/channel/group
     * @return array
     */
    public function getChatMembersCount($chatId)
    {
        try {
            $response = Http::timeout(30)->get("{$this->apiUrl}/getChatMemberCount", [
                'chat_id' => $chatId,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'count' => $response->json()['result'],
                    'message' => 'Berhasil mendapatkan jumlah member'
                ];
            }

            return [
                'success' => false,
                'error' => $response->json(),
                'message' => 'Gagal mendapatkan jumlah member'
            ];

        } catch (\Exception $e) {
            Log::error('Telegram Get Chat Members Count Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Terjadi kesalahan saat mendapatkan jumlah member'
            ];
        }
    }
}


