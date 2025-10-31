<?php

namespace WechatOfficialAccountMaterialBundle\Enum;

use ChrisUllyott\FileSize;
use Tourze\EnumExtra\BadgeInterface;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum MaterialType: string implements Labelable, Itemable, Selectable, BadgeInterface
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

    public function getBadge(): string
    {
        return match ($this) {
            self::IMAGE => 'success',
            self::VOICE => 'info',
            self::VIDEO => 'primary',
            self::THUMB => 'secondary',
        };
    }

    /**
     * 获取所有枚举的选项数组（用于下拉列表等）
     *
     * @return array<int, array{value: string, label: string}>
     */
    public static function toSelectItems(): array
    {
        $result = [];
        foreach (self::cases() as $case) {
            $result[] = [
                'value' => $case->value,
                'label' => $case->getLabel(),
            ];
        }

        return $result;
    }

    /**
     * 获取允许的文件扩展名
     *
     * @return array<string>
     */
    public function getAllowExtensions(): array
    {
        return match ($this) {
            self::IMAGE => ['bmp', 'png', 'jpeg', 'jpg', 'gif'],
            self::VOICE => ['mp3', 'wma', 'wav', 'amr'],
            self::VIDEO => ['mp4'],
            self::THUMB => ['jpg'],
        };
    }

    /**
     * 获取最大文件大小
     */
    public function getMaxFileSize(): FileSize
    {
        return match ($this) {
            self::IMAGE => new FileSize('10MB'),
            self::VOICE => new FileSize('2MB'),
            self::VIDEO => new FileSize('10MB'),
            self::THUMB => new FileSize('64KB'),
        };
    }
}
