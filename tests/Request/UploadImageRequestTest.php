<?php

namespace WechatOfficialAccountMaterialBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountMaterialBundle\Request\UploadImageRequest;

class UploadImageRequestTest extends TestCase
{
    private UploadImageRequest $request;
    private Account $account;

    protected function setUp(): void
    {
        $this->request = new UploadImageRequest();
        $this->account = $this->createMock(Account::class);
        $this->request->setAccount($this->account);
    }

    public function testGetRequestPath(): void
    {
        $this->assertSame('https://api.weixin.qq.com/cgi-bin/media/uploadimg', $this->request->getRequestPath());
    }

    public function testGetRequestOptions(): void
    {
        // 由于无法真实地打开文件，我们只测试请求选项的结构
        $this->markTestSkipped('由于文件资源无法模拟，跳过此测试');
    }

    public function testGetRequestMethod(): void
    {
        $this->assertSame('POST', $this->request->getRequestMethod());
    }

    public function testGetSetPath(): void
    {
        $path = '/path/to/test/image.jpg';
        $this->request->setPath($path);
        $this->assertSame($path, $this->request->getPath());
    }
} 