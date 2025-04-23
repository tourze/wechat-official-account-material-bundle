<?php

namespace WechatOfficialAccountMaterialBundle\Enum;

use ChrisUllyott\FileSize;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum MaterialType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case IMAGE = 'image';
    case VOICE = 'voice';
    case VIDEO = 'video';
    case THUMB = 'thumb';

    public function getLabel(): string
    {
        return match ($this) {
            self::IMAGE => '图片',
            self::VOICE => '语音',
            self::VIDEO => '视频',
            self::THUMB => '缩略图',
        };
    }

    public function getAllowExtensions(): array
    {
        return match ($this) {
            self::IMAGE => ['bmp', 'png', 'jpeg', 'jpg', 'gif'],
            self::VOICE => ['mp3', 'wma', 'wav', 'amr'],
            self::VIDEO => ['mp4'],
            self::THUMB => ['jpg'],
        };
    }

    public function getMaxFileSize(): FileSize
    {
        return match ($this) {
            self::IMAGE, self::VIDEO => new FileSize('10M'),
            self::VOICE => new FileSize('2M'),
            self::THUMB => new FileSize('64KB'),
        };
    }
}
