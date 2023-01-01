<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum TransactionStatus: string implements HasColor, HasIcon, HasLabel
{
    case PENDING = 'pending';
    case REVIEW = 'review';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING => __('Menunggu Pembayaran'),
            self::REVIEW => __('Menunggu Verifikasi'),
            self::APPROVED => __('Approved'),
            self::REJECTED => __('Ditolak'),
            self::CANCELLED => __('Dibatalkan'),
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::PENDING => 'tabler-clock-pause',
            self::REVIEW => 'tabler-clock',
            self::APPROVED => 'tabler-checklist',
            self::REJECTED => 'tabler-xbox-x',
            self::CANCELLED => 'tabler-cancel',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::PENDING => 'primary',
            self::REVIEW => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::CANCELLED => 'secondary',
        };
    }

    public static function values(): array
    {
        return [
            self::PENDING->value,
            self::REVIEW->value,
            self::APPROVED->value,
            self::REJECTED->value,
            self::CANCELLED->value,
        ];
    }
}
