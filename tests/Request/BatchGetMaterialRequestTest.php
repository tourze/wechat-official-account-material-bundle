<?php

namespace WechatOfficialAccountMaterialBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountMaterialBundle\Request\BatchGetMaterialRequest;

class BatchGetMaterialRequestTest extends TestCase
{
    private BatchGetMaterialRequest $request;
    private Account $account;

    protected function setUp(): void
    {
        $this->request = new BatchGetMaterialRequest();
        $this->account = $this->createMock(Account::class);
        $this->request->setAccount($this->account);
    }

    public function testGetRequestPath(): void
    {
        $this->assertSame('https://api.weixin.qq.com/cgi-bin/material/batchget_material', $this->request->getRequestPath());
    }

    public function testGetRequestOptions_WithDefaultValues(): void
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

    public function testGetRequestOptions_WithCustomValues(): void
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