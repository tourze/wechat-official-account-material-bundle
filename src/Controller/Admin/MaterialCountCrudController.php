<?php

declare(strict_types=1);

namespace WechatOfficialAccountMaterialBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountMaterialBundle\Entity\MaterialCount;

#[AdminCrud(routePath: '/wechat_official_account_material/count', routeName: 'wechat_official_account_material_count')]
final class MaterialCountCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MaterialCount::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('素材统计')
            ->setEntityLabelInPlural('素材统计')
            ->setSearchFields(['id'])
            ->setDefaultSort(['date' => 'DESC'])
            ->setPaginatorPageSize(30)
            ->showEntityActionsInlined()
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $action) {
                return $action->setIcon('fa fa-eye');
            })
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnIndex()
        ;

        yield AssociationField::new('account', '公众号账户')
            ->setRequired(true)
            ->setFormTypeOption('choice_label', 'name')
            ->setHelp('选择关联的微信公众号账户')
        ;

        yield DateField::new('date', '统计日期')
            ->setRequired(true)
            ->setHelp('素材统计的日期')
            ->setFormat('Y-m-d')
        ;

        yield IntegerField::new('voiceCount', '语音数量')
            ->setHelp('语音素材的总数量')
            ->setFormTypeOption('attr', ['min' => 0])
        ;

        yield IntegerField::new('videoCount', '视频数量')
            ->setHelp('视频素材的总数量')
            ->setFormTypeOption('attr', ['min' => 0])
        ;

        yield IntegerField::new('imageCount', '图片数量')
            ->setHelp('图片素材的总数量')
            ->setFormTypeOption('attr', ['min' => 0])
        ;

        yield IntegerField::new('newsCount', '图文数量')
            ->setHelp('图文素材的总数量')
            ->setFormTypeOption('attr', ['min' => 0])
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->hideOnForm()
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->hideOnForm()
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('account', '公众号账户'))
            ->add(DateTimeFilter::new('date', '统计日期'))
            ->add(NumericFilter::new('voiceCount', '语音数量'))
            ->add(NumericFilter::new('videoCount', '视频数量'))
            ->add(NumericFilter::new('imageCount', '图片数量'))
            ->add(NumericFilter::new('newsCount', '图文数量'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }
}
