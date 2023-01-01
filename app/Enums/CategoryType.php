<?php

namespace App\Enums;

enum CategoryType: string
{
    case ACCOUNT = 'account';
    case ASSET = 'asset';
    case TRANSACTION = 'transaction';
    case EVENT = 'event';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::ACCOUNT => __('Akun'),
            self::ASSET => __('Aset'),
            self::TRANSACTION => __('Transaksi'),
            self::EVENT => __('Kegiatan'),
            self::OTHER => __('Lainnya'),
        };
    }
}
