<?php

namespace WechatOfficialAccountMaterialBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountMaterialBundle\Entity\Material;
use WechatOfficialAccountMaterialBundle\Enum\MaterialType;

class MaterialTest extends TestCase
{
    private Material $material;

    protected function setUp(): void
    {
        $this->material = new Material();
    }

    public function testGetSetId(): void
    {
        // ID是由Doctrine生成的，因此我们跳过对其进行设置的测试
        $this->assertNull($this->material->getId());
    }

    public function testGetSetSyncing(): void
    {
        $this->assertFalse($this->material->isSyncing());
        
        $result = $this->material->setSyncing(true);
        $this->assertSame($this->material, $result);
        $this->assertTrue($this->material->isSyncing());
    }

    public function testGetSetAccount(): void
    {
        $account = $this->createMock(Account::class);
        
        $result = $this->material->setAccount($account);
        $this->assertSame($this->material, $result);
        $this->assertSame($account, $this->material->getAccount());
    }

    public function testGetSetType(): void
    {
        $type = MaterialType::IMAGE;
        
        $result = $this->material->setType($type);
        $this->assertSame($this->material, $result);
        $this->assertSame($type, $this->material->getType());
    }

    public function testGetSetMediaId(): void
    {
        $mediaId = 'test_media_id';
        
        $result = $this->material->setMediaId($mediaId);
        $this->assertSame($this->material, $result);
        $this->assertSame($mediaId, $this->material->getMediaId());
    }

    public function testGetSetName(): void
    {
        $name = 'test_name';
        
        $result = $this->material->setName($name);
        $this->assertSame($this->material, $result);
        $this->assertSame($name, $this->material->getName());
    }

    public function testGetSetUrl(): void
    {
        $url = 'https://example.com/test.jpg';
        
        $result = $this->material->setUrl($url);
        $this->assertSame($this->material, $result);
        $this->assertSame($url, $this->material->getUrl());
    }

    public function testGetSetContent(): void
    {
        $content = ['key' => 'value'];
        
        $result = $this->material->setContent($content);
        $this->assertSame($this->material, $result);
        $this->assertSame($content, $this->material->getContent());
    }

    public function testGetSetLocalFile(): void
    {
        $localFile = '/path/to/local/file.jpg';
        
        $result = $this->material->setLocalFile($localFile);
        $this->assertSame($this->material, $result);
        $this->assertSame($localFile, $this->material->getLocalFile());
    }

    public function testGetSetCreatedFromIp(): void
    {
        $ip = '192.168.1.1';
        
        $result = $this->material->setCreatedFromIp($ip);
        $this->assertSame($this->material, $result);
        $this->assertSame($ip, $this->material->getCreatedFromIp());
    }

    public function testGetSetUpdatedFromIp(): void
    {
        $ip = '192.168.1.2';
        
        $result = $this->material->setUpdatedFromIp($ip);
        $this->assertSame($this->material, $result);
        $this->assertSame($ip, $this->material->getUpdatedFromIp());
    }

    public function testGetSetCreateTime(): void
    {
        $dateTime = new \DateTime();
        
        $this->material->setCreateTime($dateTime);
        $this->assertSame($dateTime, $this->material->getCreateTime());
    }

    public function testGetSetUpdateTime(): void
    {
        $dateTime = new \DateTime();
        
        $this->material->setUpdateTime($dateTime);
        $this->assertSame($dateTime, $this->material->getUpdateTime());
    }
} 