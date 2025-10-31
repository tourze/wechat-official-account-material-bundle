<?php

declare(strict_types=1);

namespace WechatOfficialAccountMaterialBundle\Service;

use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\CrudMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\SectionMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use WechatOfficialAccountMaterialBundle\Controller\Admin\MaterialCountCrudController;
use WechatOfficialAccountMaterialBundle\Controller\Admin\MaterialCrudController;

class AdminMenu implements MenuProviderInterface
{
    /**
     * 获取微信公众号素材管理相关的菜单项
     *
     * @return iterable<int, MenuItem|SectionMenuItem|CrudMenuItem>
     */
    public function getMenuItems(): iterable
    {
        yield MenuItem::section('微信公众号素材', 'fas fa-images');

        yield MenuItem::linkToCrud('永久素材', 'fas fa-file-image', MaterialCrudController::getEntityFqcn())
            ->setController(MaterialCrudController::class)
        ;

        yield MenuItem::linkToCrud('素材统计', 'fas fa-chart-bar', MaterialCountCrudController::getEntityFqcn())
            ->setController(MaterialCountCrudController::class)
        ;
    }

    public function __invoke(mixed $item): void
    {
        // Implementation required by MenuProviderInterface
    }
}
