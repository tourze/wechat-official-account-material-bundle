<?php

namespace WechatOfficialAccountMaterialBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountMaterialBundle\Entity\MaterialCount;

class MaterialCountTest extends TestCase
{
    private MaterialCount $materialCount;

    protected function setUp(): void
    {
        $this->materialCount = new MaterialCount();
    }

    public function testGetId(): void
    {
        // ID是由Doctrine生成的，我们不测试设置值，只测试初始值
        $this->assertSame(0, $this->materialCount->getId());
    }

    public function testGetSetAccount(): void
    {
        $account = $this->createMock(Account::class);
        
        $result = $this->materialCount->setAccount($account);
        $this->assertSame($this->materialCount, $result);
        $this->assertSame($account, $this->materialCount->getAccount());
    }

    public function testGetSetDate(): void
    {
        $date = new \DateTime();
        
        $result = $this->materialCount->setDate($date);
        $this->assertSame($this->materialCount, $result);
        $this->assertSame($date, $this->materialCount->getDate());
    }

    public function testGetSetVoiceCount(): void
    {
        $count = 10;
        
        $result = $this->materialCount->setVoiceCount($count);
        $this->assertSame($this->materialCount, $result);
        $this->assertSame($count, $this->materialCount->getVoiceCount());
    }

    public function testGetSetVideoCount(): void
    {
        $count = 15;
        
        $result = $this->materialCount->setVideoCount($count);
        $this->assertSame($this->materialCount, $result);
        $this->assertSame($count, $this->materialCount->getVideoCount());
    }

    public function testGetSetImageCount(): void
    {
        $count = 20;
        
        $result = $this->materialCount->setImageCount($count);
        $this->assertSame($this->materialCount, $result);
        $this->assertSame($count, $this->materialCount->getImageCount());
    }

    public function testGetSetNewsCount(): void
    {
        $count = 25;
        
        $result = $this->materialCount->setNewsCount($count);
        $this->assertSame($this->materialCount, $result);
        $this->assertSame($count, $this->materialCount->getNewsCount());
    }

    public function testGetSetCreateTime(): void
    {
        $dateTime = new \DateTimeImmutable();
        
        $this->materialCount->setCreateTime($dateTime);
        $this->assertSame($dateTime, $this->materialCount->getCreateTime());
    }

    public function testGetSetUpdateTime(): void
    {
        $dateTime = new \DateTimeImmutable();
        
        $this->materialCount->setUpdateTime($dateTime);
        $this->assertSame($dateTime, $this->materialCount->getUpdateTime());
    }
} 