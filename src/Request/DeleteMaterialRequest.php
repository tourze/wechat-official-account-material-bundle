<?php

namespace WechatOfficialAccountMaterialBundle\Request;

use WechatOfficialAccountBundle\Request\WithAccountRequest;

/**
 * 删除永久素材
 *
 * @see https://developers.weixin.qq.com/doc/offiaccount/Asset_Management/Deleting_Permanent_Assets.html
 */
class DeleteMaterialRequest extends WithAccountRequest
{
    /**
     * @var string 要删除的素材的media_id
     */
    private string $mediaId;

    public function getRequestPath(): string
    {
        return 'https://api.weixin.qq.com/cgi-bin/material/del_material';
    }

    public function getRequestOptions(): ?array
    {
        $json = [
            'media_id' => $this->getMediaId(),
        ];

        return [
            'json' => $json,
        ];
    }

    public function getMediaId(): string
    {
        return $this->mediaId;
    }

    public function setMediaId(string $mediaId): void
    {
        $this->mediaId = $mediaId;
    }
}
