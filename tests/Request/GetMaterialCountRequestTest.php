<?php

namespace WechatOfficialAccountMaterialBundle\Tests\Request;

use HttpClientBundle\Test\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountMaterialBundle\Request\GetMaterialCountRequest;

/**
 * @internal
 */
#[CoversClass(GetMaterialCountRequest::class)]
final class GetMaterialCountRequestTest extends RequestTestCase
{
    private GetMaterialCountRequest $request;

    private Account $account;

    protected function setUp(): void
    {
        // Request 测试中允许直接实例化，因为需要测试请求对象的基本功能
        $this->request = new GetMaterialCountRequest();

        // 使用具体类 Account 是因为：
        // 1) 该类是 Doctrine Entity，不存在对应的接口抽象
        // 2) Request 类需要使用 Account 实例来处理微信 API 请求
        // 3) 在单元测试中模拟 Entity 是常见且合理的做法
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
