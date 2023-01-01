<?php

namespace App\Helpers;

class MediaTypeHelper
{
    /**
     * Kategori media berdasarkan mime type
     */
    const MEDIA_CATEGORIES = [
        'gif' => [
            'image/gif',
        ],
        'photo' => [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/webp',
            'image/svg+xml',
            'image/bmp',
            'image/tiff',
        ],
        'video' => [
            'video/mp4',
            'video/mpeg',
            'video/quicktime',
            'video/x-msvideo',
            'video/x-ms-wmv',
            'video/webm',
            'video/ogg',
        ],
        'audio' => [
            'audio/mpeg',
            'audio/mp3',
            'audio/wav',
            'audio/ogg',
            'audio/midi',
            'audio/x-ms-wma',
            'audio/webm',
            'audio/aac',
        ],
        'document' => [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            'text/csv',
            'application/rtf',
            'application/zip',
            'application/x-rar-compressed',
        ],
    ];

    /**
     * Mapping ekstensi file ke kategori media
     */
    const EXTENSION_MAP = [
        // Gif
        'gif' => 'gif',

        // Photo
        'jpg' => 'photo',
        'jpeg' => 'photo',
        'png' => 'photo',
        'webp' => 'photo',
        'svg' => 'photo',
        'bmp' => 'photo',
        'tiff' => 'photo',

        // Video
        'mp4' => 'video',
        'mpeg' => 'video',
        'mpg' => 'video',
        'mov' => 'video',
        'avi' => 'video',
        'wmv' => 'video',
        'webm' => 'video',
        'ogv' => 'video',
        'flv' => 'video',

        // Audio
        'mp3' => 'audio',
        'wav' => 'audio',
        'ogg' => 'audio',
        'midi' => 'audio',
        'mid' => 'audio',
        'wma' => 'audio',
        'aac' => 'audio',

        // Document
        'pdf' => 'document',
        'doc' => 'document',
        'docx' => 'document',
        'xls' => 'document',
        'xlsx' => 'document',
        'ppt' => 'document',
        'pptx' => 'document',
        'txt' => 'document',
        'csv' => 'document',
        'rtf' => 'document',
        'zip' => 'document',
        'rar' => 'document',
    ];

    /**
     * Mendapatkan kategori media dari mime type
     *
     * @param  string  $mimeType
     * @return string|null
     */
    public static function getCategoryFromMimeType($mimeType)
    {
        foreach (self::MEDIA_CATEGORIES as $category => $mimeTypes) {
            if (in_array($mimeType, $mimeTypes)) {
                return $category;
            }
        }

        // Jika tidak ditemukan, coba deteksi berdasarkan prefix
        if (str_starts_with($mimeType, 'image/') && $mimeType !== 'image/gif') {
            return 'photo';
        } elseif (str_starts_with($mimeType, 'video/')) {
            return 'video';
        } elseif (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        } elseif (str_starts_with($mimeType, 'application/') || str_starts_with($mimeType, 'text/')) {
            return 'document';
        }

        return null;
    }

    /**
     * Mendapatkan kategori media dari ekstensi file
     *
     * @param  string  $fileExtension
     * @return string|null
     */
    public static function getCategoryFromExtension($fileExtension)
    {
        $extension = strtolower($fileExtension);

        return self::EXTENSION_MAP[$extension] ?? null;
    }

    /**
     * Mendapatkan kategori media dari URL
     *
     * @param  string  $url
     * @return string|null
     */
    public static function getCategoryFromUrl($url)
    {
        $extension = strtolower(pathinfo($url, PATHINFO_EXTENSION));

        return self::getCategoryFromExtension($extension);
    }

    /**
     * Mendapatkan kategori media dari objek Media
     *
     * @param  \Spatie\MediaLibrary\MediaCollections\Models\Media  $media
     * @return string|null
     */
    public static function getCategoryFromMediaObject($media)
    {
        // Coba dari mime type terlebih dahulu
        $category = self::getCategoryFromMimeType($media->mime_type);

        // Jika tidak ditemukan, coba dari ekstensi
        if (! $category) {
            $category = self::getCategoryFromExtension($media->extension);
        }

        return $category;
    }
}
