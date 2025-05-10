<?php

namespace WechatOfficialAccountMaterialBundle\Tests\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use WechatOfficialAccountMaterialBundle\Repository\MaterialRepository;

class MaterialRepositoryTest extends TestCase
{
    private ManagerRegistry $registry;
    private MaterialRepository $repository;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->repository = new MaterialRepository($this->registry);
    }

    public function testConstructor(): void
    {
        $this->assertInstanceOf(MaterialRepository::class, $this->repository);
    }
} 