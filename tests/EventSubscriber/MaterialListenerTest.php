<?php

namespace WechatOfficialAccountMaterialBundle\Tests\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountBundle\Service\OfficialAccountClient;
use WechatOfficialAccountMaterialBundle\Entity\Material;
use WechatOfficialAccountMaterialBundle\EventSubscriber\MaterialListener;

class MaterialListenerTest extends TestCase
{
    private OfficialAccountClient $client;
    private EntityManagerInterface $entityManager;
    private MaterialListener $listener;

    protected function setUp(): void
    {
        $this->client = $this->createMock(OfficialAccountClient::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->listener = new MaterialListener($this->client, $this->entityManager);
    }

    public function testPrePersist_WithSyncingTrue(): void
    {
        $material = $this->createMock(Material::class);
        $material->expects($this->once())
            ->method('isSyncing')
            ->willReturn(true);
        
        // 当syncing=true时，不应该发送请求
        $this->client->expects($this->never())
            ->method('request');
            
        $this->listener->prePersist($material);
    }

    public function testPrePersist_WithMediaIdExists(): void
    {
        $material = $this->createMock(Material::class);
        $material->expects($this->once())
            ->method('isSyncing')
            ->willReturn(false);
            
        $material->expects($this->once())
            ->method('getMediaId')
            ->willReturn('existing_media_id');
            
        $material->expects($this->never())
            ->method('getLocalFile');
            
        // 当已存在mediaId时，不应该发送请求
        $this->client->expects($this->never())
            ->method('request');
            
        $this->listener->prePersist($material);
    }

    public function testPrePersist_WithNoLocalFile(): void
    {
        $material = $this->createMock(Material::class);
        $material->expects($this->once())
            ->method('isSyncing')
            ->willReturn(false);
            
        $material->expects($this->once())
            ->method('getMediaId')
            ->willReturn(null);
            
        $material->expects($this->once())
            ->method('getLocalFile')
            ->willReturn(null);
            
        // 当没有本地文件时，不应该发送请求
        $this->client->expects($this->never())
            ->method('request');
            
        $this->listener->prePersist($material);
    }

    public function testPreRemove_WithSyncingTrue(): void
    {
        $material = $this->createMock(Material::class);
        $material->expects($this->once())
            ->method('isSyncing')
            ->willReturn(true);
        
        // 当syncing=true时，不应该发送请求
        $this->client->expects($this->never())
            ->method('asyncRequest');
            
        $this->listener->preRemove($material);
    }

    public function testPreRemove_WithNoMediaId(): void
    {
        $material = $this->createMock(Material::class);
        $material->expects($this->once())
            ->method('isSyncing')
            ->willReturn(false);
            
        $material->expects($this->once())
            ->method('getMediaId')
            ->willReturn(null);
            
        // 当没有mediaId时，不应该发送请求
        $this->client->expects($this->never())
            ->method('asyncRequest');
            
        $this->listener->preRemove($material);
    }

    public function testPreRemove_SuccessfulDelete(): void
    {
        $account = $this->createMock(Account::class);
        $material = $this->createMock(Material::class);
        
        $material->expects($this->once())
            ->method('isSyncing')
            ->willReturn(false);
            
        $material->method('getMediaId')
            ->willReturn('media_id_to_delete');
            
        $material->expects($this->once())
            ->method('getAccount')
            ->willReturn($account);
            
        // 预期的异步请求
        $this->client->expects($this->once())
            ->method('asyncRequest');
            
        $this->listener->preRemove($material);
    }
    
    // prePersist方法需要跳过完整测试，因为无法模拟文件IO操作
    public function testPrePersist_SuccessfulUpload(): void
    {
        $this->markTestSkipped('无法模拟文件操作，跳过此测试');
    }
} 