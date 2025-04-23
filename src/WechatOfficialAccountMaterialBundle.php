<?php

namespace WechatOfficialAccountMaterialBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: '公众号素材')]
class WechatOfficialAccountMaterialBundle extends Bundle
{
}
