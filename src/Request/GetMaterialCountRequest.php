<?php

namespace WechatOfficialAccountMaterialBundle\Request;

use WechatOfficialAccountBundle\Request\WithAccountRequest;

/**
 * 获取素材总数
 *
 * @see https://developers.weixin.qq.com/doc/offiaccount/Asset_Management/Get_the_total_of_all_materials.html
 */
class GetMaterialCountRequest extends WithAccountRequest
{
    public function getRequestPath(): string
    {
        return 'https://api.weixin.qq.com/cgi-bin/material/get_materialcount';
    }

    public function getRequestOptions(): ?array
    {
        return [];
    }

    public function getRequestMethod(): ?string
    {
        return 'GET';
    }
}
