<?php

namespace WechatOfficialAccountMaterialBundle\Tests\Request;

use HttpClientBundle\Test\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountMaterialBundle\Request\BatchGetMaterialRequest;

/**
 * @internal
 */
#[CoversClass(BatchGetMaterialRequest::class)]
final class BatchGetMaterialRequestTest extends RequestTestCase
{
    private BatchGetMaterialRequest $request;

    private Account $account;

    protected function setUp(): void
    {
        // Request 测试中允许直接实例化，因为需要测试请求对象的基本功能
        $this->request = new BatchGetMaterialRequest();

        // 使用具体类 Account 是因为：
        // 1) 该类是 Doctrine Entity，不存在对应的接口抽象
        // 2) Request 类需要使用 Account 实例来处理微信 API 请求
        // 3) 在单元测试中模拟 Entity 是常见且合理的做法
        $this->account = $this->createMock(Account::class);
        $this->request->setAccount($this->account);
    }

    public function testGetRequestPath(): void
    {
        $this->assertSame('https://api.weixin.qq.com/cgi-bin/material/batchget_material', $this->request->getRequestPath());
    }

    public function testGetRequestOptionsWithDefaultValues(): void
    {
        $this->request->setType('image');

        $expectedOptions = [
            'json' => [
                'type' => 'image',
                'offset' => 0,
                'count' => 20,
            ],
        ];

        $this->assertEquals($expectedOptions, $this->request->getRequestOptions());
    }

    public function testGetRequestOptionsWithCustomValues(): void
    {
        $this->request->setType('video');
        $this->request->setOffset(10);
        $this->request->setCount(15);

        $expectedOptions = [
            'json' => [
                'type' => 'video',
                'offset' => 10,
                'count' => 15,
            ],
        ];

        $this->assertEquals($expectedOptions, $this->request->getRequestOptions());
    }

    public function testGetSetType(): void
    {
        $this->request->setType('voice');
        $this->assertSame('voice', $this->request->getType());
    }

    public function testGetSetOffset(): void
    {
        $this->request->setOffset(5);
        $this->assertSame(5, $this->request->getOffset());
    }

    public function testGetSetCount(): void
    {
        $this->request->setCount(10);
        $this->assertSame(10, $this->request->getCount());
    }
}
