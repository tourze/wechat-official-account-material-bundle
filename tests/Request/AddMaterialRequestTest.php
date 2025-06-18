<?php

namespace WechatOfficialAccountMaterialBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountMaterialBundle\Enum\MaterialType;
use WechatOfficialAccountMaterialBundle\Request\AddMaterialRequest;

class AddMaterialRequestTest extends TestCase
{
    use RequestTestTrait;
    
    private AddMaterialRequest $request;
    private Account $account;
    private array $tempFiles = [];

    protected function setUp(): void
    {
        $this->request = new AddMaterialRequest();
        $this->account = $this->createMock(Account::class);
        $this->request->setAccount($this->account);
    }
    
    protected function tearDown(): void
    {
        // 清理临时文件
        $this->cleanupTempFiles($this->tempFiles);
    }

    public function testGetRequestPath(): void
    {
        $this->assertSame('https://api.weixin.qq.com/cgi-bin/material/add_material', $this->request->getRequestPath());
    }

    public function testGetRequestOptions(): void
    {
        // 创建模拟对象
        $file = $this->createMock(UploadedFile::class);
        $this->request->setFile($file);
        $this->request->setType(MaterialType::IMAGE);
        
        // 测试基本结构，不验证资源
        $options = $this->request->getRequestOptions();
        $this->assertArrayHasKey('multipart', $options);
        $this->assertCount(2, $options['multipart']);
        
        $this->assertSame('media', $options['multipart'][0]['name']);
        // 不测试资源
        
        $this->assertSame('type', $options['multipart'][1]['name']);
        $this->assertSame('image', $options['multipart'][1]['contents']);
    }

    public function testGetSetFile(): void
    {
        $file = $this->createMock(UploadedFile::class);
        $this->request->setFile($file);
        $this->assertSame($file, $this->request->getFile());
    }

    public function testGetSetType(): void
    {
        $type = MaterialType::VIDEO;
        $this->request->setType($type);
        $this->assertSame($type, $this->request->getType());
    }
} 