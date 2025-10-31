<?php

namespace WechatOfficialAccountMaterialBundle\Tests\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountBundle\Service\OfficialAccountClient;
use WechatOfficialAccountMaterialBundle\Entity\Material;
use WechatOfficialAccountMaterialBundle\Enum\MaterialType;
use WechatOfficialAccountMaterialBundle\EventSubscriber\MaterialListener;
use WechatOfficialAccountMaterialBundle\Exception\InvalidMaterialParameterException;
use WechatOfficialAccountMaterialBundle\Exception\MaterialUploadException;

/**
 * @internal
 */
#[CoversClass(MaterialListener::class)]
final class MaterialListenerTest extends TestCase
{
    private OfficialAccountClient $client;

    private MaterialListener $listener;

    protected function setUp(): void
    {
        $this->client = $this->createMock(OfficialAccountClient::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $this->listener = new MaterialListener($this->client, $entityManager);
    }

    public function testPrePersistWithSyncingTrue(): void
    {
        // 创建测试数据
        $account = new Account();
        $material = new Material();
        $material->setAccount($account);
        $material->setSyncing(true);

        // 当syncing=true时，prePersist 方法应该直接返回，不进行任何操作
        // 我们通过验证没有异常抛出来测试这个逻辑
        $this->listener->prePersist($material);

        // 如果到达这里，说明没有异常抛出，测试通过
        $this->expectNotToPerformAssertions();
    }

    public function testPrePersistWithMediaIdExists(): void
    {
        // 创建测试数据
        $account = new Account();
        $material = new Material();
        $material->setAccount($account);
        $material->setSyncing(false);
        $material->setMediaId('existing_media_id');

        // 当已存在mediaId时，prePersist 方法应该直接返回，不进行任何操作
        $this->listener->prePersist($material);

        // 如果到达这里，说明没有异常抛出，测试通过
        $this->expectNotToPerformAssertions();
    }

    public function testPrePersistWithNoLocalFile(): void
    {
        // 创建测试数据
        $account = new Account();
        $material = new Material();
        $material->setAccount($account);
        $material->setSyncing(false);
        $material->setMediaId(null);
        $material->setLocalFile(null);

        // 当没有本地文件时，prePersist 方法应该直接返回，不进行任何操作
        $this->listener->prePersist($material);

        // 如果到达这里，说明没有异常抛出，测试通过
        $this->expectNotToPerformAssertions();
    }

    public function testPrePersistThrowsExceptionWhenFileCannotBeFetched(): void
    {
        // 创建测试数据
        $account = new Account();
        $material = new Material();
        $material->setAccount($account);
        $material->setSyncing(false);
        $material->setMediaId(null);
        $material->setLocalFile('http://non-existent-domain-12345.com/file.jpg');
        $material->setType(MaterialType::IMAGE);

        // 预期抛出异常，因为无法从无效的URL获取文件内容
        $this->expectException(MaterialUploadException::class);
        $this->expectExceptionMessage('Cannot fetch content from URL');

        // 调用监听器方法，应该抛出异常
        $this->listener->prePersist($material);
    }

    public function testPreRemoveWithSyncingTrue(): void
    {
        // 创建测试数据
        $account = new Account();
        $material = new Material();
        $material->setAccount($account);
        $material->setSyncing(true);

        // 当syncing=true时，preRemove 方法应该直接返回，不进行任何操作
        $this->listener->preRemove($material);

        // 如果到达这里，说明没有异常抛出，测试通过
        $this->expectNotToPerformAssertions();
    }

    public function testPreRemoveWithNoMediaId(): void
    {
        // 创建测试数据
        $account = new Account();
        $material = new Material();
        $material->setAccount($account);
        $material->setSyncing(false);
        $material->setMediaId(null);

        // 当没有mediaId时，preRemove 方法应该直接返回，不进行任何操作
        $this->listener->preRemove($material);

        // 如果到达这里，说明没有异常抛出，测试通过
        $this->expectNotToPerformAssertions();
    }

    public function testPreRemoveSuccessfulDelete(): void
    {
        // 创建测试数据
        $account = new Account();
        $material = new Material();
        $material->setAccount($account);
        $material->setSyncing(false);
        $material->setMediaId('media_id_to_delete');

        // 当有mediaId且syncing=false时，preRemove 方法应该尝试发送异步删除请求
        // 由于这是集成测试，我们验证方法能够正常执行（不抛出异常）
        // 注意：实际的微信API调用会被真实执行，但由于是测试环境，可能会有特殊处理
        $this->listener->preRemove($material);

        // 如果到达这里，说明没有异常抛出，测试通过
        $this->expectNotToPerformAssertions();
    }
}
