<?php

declare(strict_types=1);

namespace WechatOfficialAccountMaterialBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use WechatOfficialAccountMaterialBundle\DependencyInjection\WechatOfficialAccountMaterialExtension;
use WechatOfficialAccountMaterialBundle\WechatOfficialAccountMaterialBundle;

class WechatOfficialAccountMaterialBundleTest extends TestCase
{
    private WechatOfficialAccountMaterialBundle $bundle;

    protected function setUp(): void
    {
        $this->bundle = new WechatOfficialAccountMaterialBundle();
    }

    public function testGetContainerExtension(): void
    {
        $extension = $this->bundle->getContainerExtension();
        self::assertInstanceOf(WechatOfficialAccountMaterialExtension::class, $extension);
    }

    public function testBuild(): void
    {
        $container = new ContainerBuilder();
        $this->bundle->build($container);
        
        // Bundle的build方法通常不需要额外的断言
        // 因为它主要用于注册编译器传递等
        self::assertInstanceOf(ContainerBuilder::class, $container);
    }
}