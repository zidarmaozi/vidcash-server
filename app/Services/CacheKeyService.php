<?php

namespace App\Services;

class CacheKeyService
{
    /**
     * Cache key for video settings.
     *
     * @param string|null $videoCode
     * @return string
     */
    public static function settings(?string $videoCode): string
    {
        return $videoCode ? "settings_{$videoCode}" : "settings_default";
    }

    /**
     * Cache key for related videos.
     *
     * @param string|null $videoCode
     * @return string
     */
    public static function relatedVideos(?string $videoCode): string
    {
        if (!$videoCode) {
            // Fallback or specific logic if videoCode is null, though currently logic implies it might be used with null in some contexts?
            // In ServiceController lines 34-45, $videoCode can be null but string interpolation handles it as empty string?
            // "related_videos_" . null -> "related_videos_"
            return "related_videos_";
        }
        return "related_videos_{$videoCode}";
    }

    /**
     * Cache key for folder videos.
     *
     * @param string $slug
     * @return string
     */
    public static function folderVideos(string $slug): string
    {
        return "folder_videos_{$slug}";
    }

    /**
     * Cache key for recommended folders.
     *
     * @return string
     */
    public static function recommendedFolders(): string
    {
        return "recommended_folders";
    }
}
