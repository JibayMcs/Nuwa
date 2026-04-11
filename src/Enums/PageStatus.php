<?php

namespace JibayMcs\Nuwa\Enums;

enum PageStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Scheduled = 'scheduled';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => __('nuwa::nuwa.status.draft'),
            self::Published => __('nuwa::nuwa.status.published'),
            self::Scheduled => __('nuwa::nuwa.status.scheduled'),
            self::Archived => __('nuwa::nuwa.status.archived'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Published => 'success',
            self::Scheduled => 'warning',
            self::Archived => 'danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Draft => 'heroicon-o-pencil',
            self::Published => 'heroicon-o-check-circle',
            self::Scheduled => 'heroicon-o-clock',
            self::Archived => 'heroicon-o-archive-box',
        };
    }
}
