<?php

declare(strict_types=1);

namespace WechatOfficialAccountMaterialBundle\Tests\Service;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Menu\MenuItemInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;
use WechatOfficialAccountMaterialBundle\Service\AdminMenu;

/**
 * 微信公众号素材管理菜单服务测试
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    private AdminMenu $adminMenu;

    private LinkGeneratorInterface $linkGenerator;

    protected function onSetUp(): void
    {
        $this->linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        self::getContainer()->set(LinkGeneratorInterface::class, $this->linkGenerator);
        $this->adminMenu = self::getService(AdminMenu::class);
    }

    public function testServiceIsCallable(): void
    {
        self::assertIsCallable($this->adminMenu);
    }

    public function testGetMenuItems(): void
    {
        $menuItems = iterator_to_array($this->adminMenu->getMenuItems());

        self::assertNotEmpty($menuItems);
        self::assertGreaterThanOrEqual(3, count($menuItems), '应该至少包含section和两个CRUD菜单项');

        // 检查菜单项类型
        $sectionCount = 0;
        $crudCount = 0;

        foreach ($menuItems as $item) {
            // MenuItem::section() 返回 SectionMenuItem
            // MenuItem::linkToCrud() 返回 CrudMenuItem
            // 它们都实现了 MenuItemInterface 并有 getAsDto() 方法
            self::assertInstanceOf(MenuItemInterface::class, $item);
            $dto = $item->getAsDto();
            $type = $dto->getType();

            if ('section' === $type) {
                ++$sectionCount;
            } elseif ('crud' === $type) {
                ++$crudCount;
            }
        }

        self::assertGreaterThanOrEqual(1, $sectionCount, '应该包含至少一个section');
        self::assertGreaterThanOrEqual(2, $crudCount, '应该包含至少两个CRUD菜单项');
    }

    public function testImplementsMenuProviderInterface(): void
    {
        // 验证服务实现了正确的接口，通过反射检查而非类型检查
        $reflection = new \ReflectionClass($this->adminMenu);
        $interfaces = $reflection->getInterfaceNames();
        self::assertContains(MenuProviderInterface::class, $interfaces);
    }

    public function testInvokeMethod(): void
    {
        // 测试__invoke方法不会抛出异常
        $this->adminMenu->__invoke(null);
        $this->adminMenu->__invoke('test');
        $this->adminMenu->__invoke(['test']);

        // 确保方法执行完成
        $this->assertTrue(true, '__invoke方法执行完成');
    }
}
