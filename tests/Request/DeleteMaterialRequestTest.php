<?php

namespace WechatOfficialAccountMaterialBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountMaterialBundle\Request\DeleteMaterialRequest;

class DeleteMaterialRequestTest extends TestCase
{
    private DeleteMaterialRequest $request;
    private Account $account;

    protected function setUp(): void
    {
        $this->request = new DeleteMaterialRequest();
        $this->account = $this->createMock(Account::class);
        $this->request->setAccount($this->account);
    }

    public function testGetRequestPath(): void
    {
        $this->assertSame('https://api.weixin.qq.com/cgi-bin/material/del_material', $this->request->getRequestPath());
    }

    public function testGetRequestOptions(): void
    {
        $mediaId = 'test_media_id';
        $this->request->setMediaId($mediaId);
        
        $expectedOptions = [
            'json' => [
                'media_id' => $mediaId,
            ],
        ];
        
        $this->assertEquals($expectedOptions, $this->request->getRequestOptions());
    }

    public function testGetSetMediaId(): void
    {
        $mediaId = 'another_test_media_id';
        $this->request->setMediaId($mediaId);
        $this->assertSame($mediaId, $this->request->getMediaId());
    }
} 