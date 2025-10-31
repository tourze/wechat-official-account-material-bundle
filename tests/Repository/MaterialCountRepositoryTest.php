<?php

namespace WechatOfficialAccountMaterialBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountMaterialBundle\Entity\MaterialCount;
use WechatOfficialAccountMaterialBundle\Repository\MaterialCountRepository;

/**
 * @internal
 */
#[CoversClass(MaterialCountRepository::class)]
#[RunTestsInSeparateProcesses]
final class MaterialCountRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testConstructor(): void
    {
        $repository = self::getService(MaterialCountRepository::class);
        $this->assertInstanceOf(MaterialCountRepository::class, $repository);
    }

    public function testGetEntityClass(): void
    {
        $repository = self::getService(MaterialCountRepository::class);
        $this->assertInstanceOf(MaterialCountRepository::class, $repository);
        $this->assertSame(MaterialCount::class, $repository->getClassName());
    }

    public function testFindOneByWithOrderByClause(): void
    {
        $account = new Account();
        $account->setName('Test Account');
        $account->setAppId('test_app_id');
        $account->setAppSecret('test_app_secret');
        $account->setValid(true);

        $materialCount1 = new MaterialCount();
        $materialCount1->setAccount($account);
        $materialCount1->setDate(new \DateTimeImmutable('2023-01-01'));
        $materialCount1->setVoiceCount(5);

        $materialCount2 = new MaterialCount();
        $materialCount2->setAccount($account);
        $materialCount2->setDate(new \DateTimeImmutable('2023-01-02'));
        $materialCount2->setVoiceCount(10);

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->persist($materialCount1);
        $entityManager->persist($materialCount2);
        $entityManager->flush();

        $repository = self::getService(MaterialCountRepository::class);
        $result = $repository->findOneBy(['account' => $account], ['voiceCount' => 'DESC']);

        $this->assertInstanceOf(MaterialCount::class, $result);
        $this->assertSame(10, $result->getVoiceCount());
    }

    public function testFindByAccountAssociation(): void
    {
        $account1 = new Account();
        $account1->setName('Account 1');
        $account1->setAppId('app_id_1');
        $account1->setAppSecret('app_secret_1');
        $account1->setValid(true);

        $account2 = new Account();
        $account2->setName('Account 2');
        $account2->setAppId('app_id_2');
        $account2->setAppSecret('app_secret_2');
        $account2->setValid(true);

        $materialCount1 = new MaterialCount();
        $materialCount1->setAccount($account1);
        $materialCount1->setDate(new \DateTimeImmutable('2023-01-01'));
        $materialCount1->setVoiceCount(10);

        $materialCount2 = new MaterialCount();
        $materialCount2->setAccount($account2);
        $materialCount2->setDate(new \DateTimeImmutable('2023-01-01'));
        $materialCount2->setVoiceCount(20);

        $entityManager = self::getEntityManager();
        $entityManager->persist($account1);
        $entityManager->persist($account2);
        $entityManager->persist($materialCount1);
        $entityManager->persist($materialCount2);
        $entityManager->flush();

        $repository = self::getService(MaterialCountRepository::class);
        $results = $repository->findBy(['account' => $account1]);

        $this->assertCount(1, $results);
        $this->assertSame($materialCount1->getId(), $results[0]->getId());
    }

    public function testCountWithAccountAssociation(): void
    {
        $account = new Account();
        $account->setName('Test Account');
        $account->setAppId('test_app_id');
        $account->setAppSecret('test_app_secret');
        $account->setValid(true);

        $materialCount1 = new MaterialCount();
        $materialCount1->setAccount($account);
        $materialCount1->setDate(new \DateTimeImmutable('2023-01-01'));

        $materialCount2 = new MaterialCount();
        $materialCount2->setAccount($account);
        $materialCount2->setDate(new \DateTimeImmutable('2023-01-02'));

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->persist($materialCount1);
        $entityManager->persist($materialCount2);
        $entityManager->flush();

        $repository = self::getService(MaterialCountRepository::class);
        $count = $repository->count(['account' => $account]);

        $this->assertSame(2, $count);
    }

    public function testFindByNullVoiceCount(): void
    {
        $account = new Account();
        $account->setName('Test Account');
        $account->setAppId('test_app_id');
        $account->setAppSecret('test_app_secret');
        $account->setValid(true);

        $materialCount = new MaterialCount();
        $materialCount->setAccount($account);
        $materialCount->setDate(new \DateTimeImmutable('2023-01-01'));
        $materialCount->setVoiceCount(null);

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->persist($materialCount);
        $entityManager->flush();

        $repository = self::getService(MaterialCountRepository::class);
        $results = $repository->findBy(['voiceCount' => null]);

        $this->assertCount(1, $results);
        $this->assertNull($results[0]->getVoiceCount());
    }

    public function testFindByNullVideoCount(): void
    {
        $account = new Account();
        $account->setName('Test Account');
        $account->setAppId('test_app_id');
        $account->setAppSecret('test_app_secret');
        $account->setValid(true);

        $materialCount = new MaterialCount();
        $materialCount->setAccount($account);
        $materialCount->setDate(new \DateTimeImmutable('2023-01-01'));
        $materialCount->setVideoCount(null);

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->persist($materialCount);
        $entityManager->flush();

        $repository = self::getService(MaterialCountRepository::class);
        $results = $repository->findBy(['videoCount' => null]);

        $this->assertCount(1, $results);
        $this->assertNull($results[0]->getVideoCount());
    }

    public function testCountWithNullVoiceCount(): void
    {
        $account = new Account();
        $account->setName('Test Account');
        $account->setAppId('test_app_id');
        $account->setAppSecret('test_app_secret');
        $account->setValid(true);

        $materialCount = new MaterialCount();
        $materialCount->setAccount($account);
        $materialCount->setDate(new \DateTimeImmutable('2023-01-01'));
        $materialCount->setVoiceCount(null);

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->persist($materialCount);
        $entityManager->flush();

        $repository = self::getService(MaterialCountRepository::class);
        $count = $repository->count(['voiceCount' => null]);

        $this->assertSame(1, $count);
    }

    public function testSaveWithFlush(): void
    {
        $account = new Account();
        $account->setName('Test Account');
        $account->setAppId('test_app_id');
        $account->setAppSecret('test_app_secret');
        $account->setValid(true);

        $materialCount = new MaterialCount();
        $materialCount->setAccount($account);
        $materialCount->setDate(new \DateTimeImmutable('2023-01-01'));
        $materialCount->setVoiceCount(10);

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        $repository = self::getService(MaterialCountRepository::class);
        $repository->save($materialCount, true);

        $found = $repository->find($materialCount->getId());
        $this->assertInstanceOf(MaterialCount::class, $found);
        $this->assertSame(10, $found->getVoiceCount());
    }

    public function testSaveWithoutFlush(): void
    {
        $account = new Account();
        $account->setName('Test Account');
        $account->setAppId('test_app_id');
        $account->setAppSecret('test_app_secret');
        $account->setValid(true);

        $materialCount = new MaterialCount();
        $materialCount->setAccount($account);
        $materialCount->setDate(new \DateTimeImmutable('2023-01-01'));
        $materialCount->setVoiceCount(10);

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        $repository = self::getService(MaterialCountRepository::class);
        $repository->save($materialCount, false);

        $this->assertNull($materialCount->getId());

        $entityManager->flush();
        $found = $repository->find($materialCount->getId());
        $this->assertInstanceOf(MaterialCount::class, $found);
        $this->assertSame(10, $found->getVoiceCount());
    }

    protected function createNewEntity(): object
    {
        $entity = new MaterialCount();

        // 设置必需字段
        $entity->setDate(new \DateTimeImmutable());
        $entity->setAccount($this->createAccount());

        return $entity;
    }

    private function createAccount(): Account
    {
        $account = new Account();
        $account->setName('Test Account ' . uniqid());
        $account->setAppId('test_app_' . uniqid());
        $account->setAppSecret('test_secret_' . uniqid());
        $account->setValid(true);

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        return $account;
    }

    /**
     * @return ServiceEntityRepository<MaterialCount>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return self::getService(MaterialCountRepository::class);
    }
}
