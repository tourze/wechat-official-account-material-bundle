<?php

namespace WechatOfficialAccountMaterialBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountMaterialBundle\Entity\Material;
use WechatOfficialAccountMaterialBundle\Enum\MaterialType;
use WechatOfficialAccountMaterialBundle\Repository\MaterialRepository;

/**
 * @internal
 */
#[CoversClass(MaterialRepository::class)]
#[RunTestsInSeparateProcesses]
final class MaterialRepositoryTest extends AbstractRepositoryTestCase
{
    private ?Account $testAccount = null;

    protected function onSetUp(): void
    {
        // 清理数据并创建一些测试数据
        $entityManager = self::getEntityManager();
        try {
            // Delete all materials efficiently using DQL
            $entityManager->createQuery('DELETE FROM ' . Material::class . ' m')->execute();
            $entityManager->clear();

            // 创建一个测试账号
            $account = new Account();
            $account->setName('测试账号');
            $account->setAppId('test_app_id');
            $account->setAppSecret('test_app_secret');
            $account->setValid(true);
            $entityManager->persist($account);

            // 创建一些测试素材
            $material1 = new Material();
            $material1->setAccount($account);
            $material1->setType(MaterialType::IMAGE);
            $material1->setName('测试图片素材');
            $material1->setMediaId('test_media_001');
            $entityManager->persist($material1);

            $material2 = new Material();
            $material2->setAccount($account);
            $material2->setType(MaterialType::VOICE);
            $material2->setName('测试语音素材');
            $material2->setMediaId('test_media_002');
            $entityManager->persist($material2);

            $entityManager->flush();
        } catch (\Exception $e) {
            // Ignore errors during cleanup
        }
    }

    public function testConstructor(): void
    {
        // 从容器中获取真实的 MaterialRepository 服务
        // 这样测试的是真实配置下的Repository，而不是Mock的版本
        $repository = self::getService(MaterialRepository::class);
        $this->assertInstanceOf(MaterialRepository::class, $repository);
    }

    public function testGetEntityClass(): void
    {
        $repository = self::getService(MaterialRepository::class);
        $this->assertInstanceOf(MaterialRepository::class, $repository);
        $this->assertSame(Material::class, $repository->getClassName());
    }

    public function testFindOneByWithOrderByClause(): void
    {
        $account = $this->getOrCreateTestAccount();

        $uniqueId = uniqid();
        $material1 = new Material();
        $material1->setAccount($account);
        $material1->setType(MaterialType::IMAGE);
        $material1->setName('AAAA_' . $uniqueId);

        $material2 = new Material();
        $material2->setAccount($account);
        $material2->setType(MaterialType::IMAGE);
        $material2->setName('ZZZZ_' . $uniqueId);

        $entityManager = self::getEntityManager();
        $entityManager->persist($material1);
        $entityManager->persist($material2);
        $entityManager->flush();

        $repository = self::getService(MaterialRepository::class);
        $result = $repository->findOneBy(['type' => MaterialType::IMAGE], ['name' => 'DESC']);

        $this->assertInstanceOf(Material::class, $result);
        // 按照 DESC 排序，中文字符在 Unicode 中的值比英文字母大
        // 所以 '测试图片素材' 会排在 'ZZZZ_' 前面
        $this->assertSame('测试图片素材', $result->getName());
    }

    public function testFindByAccountAssociation(): void
    {
        $account1 = $this->getOrCreateTestAccount();

        $account2 = new Account();
        $account2->setName('Account 2');
        $account2->setAppId('app_id_2');
        $account2->setAppSecret('app_secret_2');
        $account2->setValid(true);

        $material1 = new Material();
        $material1->setAccount($account1);
        $material1->setType(MaterialType::IMAGE);
        $material1->setName('Material 1');

        $material2 = new Material();
        $material2->setAccount($account2);
        $material2->setType(MaterialType::IMAGE);
        $material2->setName('Material 2');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account2);
        $entityManager->persist($material1);
        $entityManager->persist($material2);
        $entityManager->flush();

        $repository = self::getService(MaterialRepository::class);
        $results = $repository->findBy(['account' => $account1]);

        $this->assertCount(1, $results);
        $this->assertSame($material1->getId(), $results[0]->getId());
    }

    public function testCountWithAccountAssociation(): void
    {
        $account = $this->getOrCreateTestAccount();

        $material1 = new Material();
        $material1->setAccount($account);
        $material1->setType(MaterialType::IMAGE);
        $material1->setName('Material 1');

        $material2 = new Material();
        $material2->setAccount($account);
        $material2->setType(MaterialType::VIDEO);
        $material2->setName('Material 2');

        $entityManager = self::getEntityManager();
        $entityManager->persist($material1);
        $entityManager->persist($material2);
        $entityManager->flush();

        $repository = self::getService(MaterialRepository::class);
        $count = $repository->count(['account' => $account]);

        $this->assertSame(2, $count);
    }

    public function testFindByNullMediaId(): void
    {
        $account = $this->getOrCreateTestAccount();

        $material = new Material();
        $material->setAccount($account);
        $material->setType(MaterialType::IMAGE);
        $material->setName('Test Material');
        $material->setMediaId(null);

        $entityManager = self::getEntityManager();
        $entityManager->persist($material);
        $entityManager->flush();

        $repository = self::getService(MaterialRepository::class);
        $results = $repository->findBy(['mediaId' => null]);

        $this->assertCount(1, $results);
        $this->assertNull($results[0]->getMediaId());
    }

    public function testFindByNullUrl(): void
    {
        $account = $this->getOrCreateTestAccount();

        $material = new Material();
        $material->setAccount($account);
        $material->setType(MaterialType::IMAGE);
        $material->setName('Test Material');
        $material->setUrl(null);

        $entityManager = self::getEntityManager();
        $entityManager->persist($material);
        $entityManager->flush();

        $repository = self::getService(MaterialRepository::class);
        $results = $repository->findBy(['url' => null]);

        // 至少找到我们刚创建的记录
        $this->assertGreaterThanOrEqual(1, count($results));

        // 验证确实找到了我们创建的记录
        $found = false;
        foreach ($results as $result) {
            $this->assertNull($result->getUrl());
            if ('Test Material' === $result->getName()) {
                $found = true;
            }
        }
        $this->assertTrue($found, 'Should find the material we just created');
    }

    public function testCountWithNullMediaId(): void
    {
        $account = $this->getOrCreateTestAccount();

        $material = new Material();
        $material->setAccount($account);
        $material->setType(MaterialType::IMAGE);
        $material->setName('Test Material');
        $material->setMediaId(null);

        $entityManager = self::getEntityManager();
        $entityManager->persist($material);
        $entityManager->flush();

        $repository = self::getService(MaterialRepository::class);
        $count = $repository->count(['mediaId' => null]);

        $this->assertSame(1, $count);
    }

    public function testSaveWithFlush(): void
    {
        $account = $this->getOrCreateTestAccount();

        $material = new Material();
        $material->setAccount($account);
        $material->setType(MaterialType::IMAGE);
        $material->setName('Test Material');
        $material->setMediaId('test_media_id');

        $repository = self::getService(MaterialRepository::class);
        $repository->save($material, true);

        $found = $repository->find($material->getId());
        $this->assertInstanceOf(Material::class, $found);
        $this->assertSame('Test Material', $found->getName());
    }

    public function testSaveWithoutFlush(): void
    {
        $account = $this->getOrCreateTestAccount();

        $material = new Material();
        $material->setAccount($account);
        $material->setType(MaterialType::IMAGE);
        $material->setName('Test Material');
        $material->setMediaId('test_media_id');

        $repository = self::getService(MaterialRepository::class);
        $repository->save($material, false);

        // After save without flush, the entity is persisted but not yet flushed
        // The ID might already be assigned if using IDENTITY generation strategy
        $this->assertNotNull($material->getId());

        $entityManager = self::getEntityManager();
        $entityManager->flush();
        $found = $repository->find($material->getId());
        $this->assertInstanceOf(Material::class, $found);
        $this->assertSame('Test Material', $found->getName());
    }

    protected function createNewEntity(): object
    {
        $entity = new Material();

        // 设置必需字段
        $entity->setType(MaterialType::IMAGE);
        $entity->setAccount($this->getOrCreateTestAccount());

        return $entity;
    }

    private function getOrCreateTestAccount(): Account
    {
        if (null === $this->testAccount) {
            $this->testAccount = new Account();
            $this->testAccount->setName('Test Account ' . uniqid());
            $this->testAccount->setAppId('test_app_' . uniqid());
            $this->testAccount->setAppSecret('test_secret_' . uniqid());
            $this->testAccount->setValid(true);

            $entityManager = self::getEntityManager();
            $entityManager->persist($this->testAccount);
            $entityManager->flush();
        }

        return $this->testAccount;
    }

    /**
     * @return ServiceEntityRepository<Material>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return self::getService(MaterialRepository::class);
    }
}
