<?php

namespace WechatOfficialAccountMaterialBundle\Tests\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use WechatOfficialAccountMaterialBundle\Repository\MaterialCountRepository;

class MaterialCountRepositoryTest extends TestCase
{
    private ManagerRegistry $registry;
    private MaterialCountRepository $repository;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->repository = new MaterialCountRepository($this->registry);
    }

    public function testConstructor(): void
    {
        $this->assertInstanceOf(MaterialCountRepository::class, $this->repository);
    }
} 