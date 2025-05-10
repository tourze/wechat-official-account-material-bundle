<?php

namespace WechatOfficialAccountMaterialBundle\Tests\Enum;

use ChrisUllyott\FileSize;
use PHPUnit\Framework\TestCase;
use WechatOfficialAccountMaterialBundle\Enum\MaterialType;

class MaterialTypeTest extends TestCase
{
    public function testCases(): void
    {
        $cases = MaterialType::cases();
        $this->assertCount(4, $cases);
        
        $values = array_map(fn($case) => $case->value, $cases);
        $this->assertContains('image', $values);
        $this->assertContains('voice', $values);
        $this->assertContains('video', $values);
        $this->assertContains('thumb', $values);
    }

    public function testGetLabel(): void
    {
        $this->assertSame('图片', MaterialType::IMAGE->getLabel());
        $this->assertSame('语音', MaterialType::VOICE->getLabel());
        $this->assertSame('视频', MaterialType::VIDEO->getLabel());
        $this->assertSame('缩略图', MaterialType::THUMB->getLabel());
    }
    
    public function testGetAllowExtensions(): void
    {
        $this->assertSame(['bmp', 'png', 'jpeg', 'jpg', 'gif'], MaterialType::IMAGE->getAllowExtensions());
        $this->assertSame(['mp3', 'wma', 'wav', 'amr'], MaterialType::VOICE->getAllowExtensions());
        $this->assertSame(['mp4'], MaterialType::VIDEO->getAllowExtensions());
        $this->assertSame(['jpg'], MaterialType::THUMB->getAllowExtensions());
    }
    
    public function testGetMaxFileSize(): void
    {
        $this->assertInstanceOf(FileSize::class, MaterialType::IMAGE->getMaxFileSize());
        $this->assertInstanceOf(FileSize::class, MaterialType::VOICE->getMaxFileSize());
        $this->assertInstanceOf(FileSize::class, MaterialType::VIDEO->getMaxFileSize());
        $this->assertInstanceOf(FileSize::class, MaterialType::THUMB->getMaxFileSize());
        
        $this->assertMatchesRegularExpression('/10\s*M[B]?/', (string)MaterialType::IMAGE->getMaxFileSize());
        $this->assertMatchesRegularExpression('/2\s*M[B]?/', (string)MaterialType::VOICE->getMaxFileSize());
        $this->assertMatchesRegularExpression('/10\s*M[B]?/', (string)MaterialType::VIDEO->getMaxFileSize());
        $this->assertMatchesRegularExpression('/64\s*K[B]?/', (string)MaterialType::THUMB->getMaxFileSize());
    }
} 