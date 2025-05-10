<?php

namespace WechatOfficialAccountMaterialBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountMaterialBundle\Request\GetMaterialCountRequest;

class GetMaterialCountRequestTest extends TestCase
{
    private GetMaterialCountRequest $request;
    private Account $account;

    protected function setUp(): void
    {
        $this->request = new GetMaterialCountRequest();
        $this->account = $this->createMock(Account::class);
        $this->request->setAccount($this->account);
    }

    public function testGetRequestPath(): void
    {
        $this->assertSame('https://api.weixin.qq.com/cgi-bin/material/get_materialcount', $this->request->getRequestPath());
    }

    public function testGetRequestOptions(): void
    {
        $this->assertEquals([], $this->request->getRequestOptions());
    }
    
    public function testGetRequestMethod(): void
    {
        $this->assertEquals('GET', $this->request->getRequestMethod());
    }
} 