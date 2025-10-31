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
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountMaterialBundle\Entity\Material;
use WechatOfficialAccountMaterialBundle\Enum\MaterialType;

#[AdminCrud(routePath: '/wechat_official_account_material/material', routeName: 'wechat_official_account_material_material')]
final class MaterialCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Material::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('永久素材')
            ->setEntityLabelInPlural('永久素材')
            ->setSearchFields(['id', 'name', 'mediaId', 'url', 'localFile'])
            ->setDefaultSort(['id' => 'DESC'])
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

        $typeField = EnumField::new('type', '素材类型');
        $typeField->setEnumCases(MaterialType::cases());
        yield $typeField
            ->setRequired(true)
            ->setHelp('选择素材的类型：图片、语音、视频或缩略图')
        ;

        yield TextField::new('mediaId', '媒体ID')
            ->setMaxLength(120)
            ->setHelp('微信服务器返回的永久素材media_id')
            ->hideOnIndex()
        ;

        yield TextField::new('name', '素材名称')
            ->setMaxLength(120)
            ->setHelp('素材的显示名称')
        ;

        yield UrlField::new('url', '素材URL')
            ->setHelp('图片素材的访问URL地址')
            ->hideOnIndex()
        ;

        yield TextField::new('localFile', '本地文件路径')
            ->setMaxLength(255)
            ->setHelp('素材在本地服务器的存储路径')
            ->hideOnIndex()
        ;

        yield TextareaField::new('content', '素材内容')
            ->setHelp('素材的详细内容信息，JSON格式')
            ->onlyOnDetail()
        ;

        yield BooleanField::new('syncing', '同步中')
            ->setHelp('标识素材是否正在与微信服务器同步')
            ->hideOnForm()
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
            ->add(ChoiceFilter::new('type', '素材类型')->setChoices([
                '图片' => MaterialType::IMAGE->value,
                '语音' => MaterialType::VOICE->value,
                '视频' => MaterialType::VIDEO->value,
                '缩略图' => MaterialType::THUMB->value,
            ]))
            ->add(TextFilter::new('name', '素材名称'))
            ->add(TextFilter::new('mediaId', '媒体ID'))
            ->add(BooleanFilter::new('syncing', '同步中'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }
}
