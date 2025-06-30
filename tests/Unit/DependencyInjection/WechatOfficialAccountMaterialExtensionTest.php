<?php

declare(strict_types=1);

namespace WechatOfficialAccountMaterialBundle\Tests\Unit\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use WechatOfficialAccountMaterialBundle\DependencyInjection\WechatOfficialAccountMaterialExtension;

class WechatOfficialAccountMaterialExtensionTest extends TestCase
{
    private WechatOfficialAccountMaterialExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new WechatOfficialAccountMaterialExtension();
        $this->container = new ContainerBuilder();
    }

    public function testLoad(): void
    {
        $configs = [];
        $this->extension->load($configs, $this->container);

        // 验证服务是否被正确加载
        self::assertTrue($this->container->has('WechatOfficialAccountMaterialBundle\Command\SyncMaterialCommand'));
        self::assertTrue($this->container->has('WechatOfficialAccountMaterialBundle\Command\SyncMaterialCountCommand'));
        self::assertTrue($this->container->has('WechatOfficialAccountMaterialBundle\EventSubscriber\MaterialListener'));
        self::assertTrue($this->container->has('WechatOfficialAccountMaterialBundle\Repository\MaterialRepository'));
        self::assertTrue($this->container->has('WechatOfficialAccountMaterialBundle\Repository\MaterialCountRepository'));
    }
}